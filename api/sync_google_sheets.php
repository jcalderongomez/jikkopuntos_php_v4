<?php
header('Content-Type: application/json');
require_once '../config/database.php';

// Obtener datos JSON del request
$input = json_decode(file_get_contents('php://input'), true);
$table = $input['table'] ?? '';

if (empty($table)) {
    echo json_encode(['success' => false, 'message' => 'Tabla no especificada']);
    exit;
}

// Cargar configuración de Google Sheets
$configFile = '../config/google_sheets_config.json';
if (!file_exists($configFile)) {
    echo json_encode(['success' => false, 'message' => 'Configuración de Google Sheets no encontrada. Configure primero en la sección de Sincronización.']);
    exit;
}

$config = json_decode(file_get_contents($configFile), true);

// Obtener la URL CSV correspondiente
$csvUrls = $config['csv_urls'] ?? [];
$csvUrl = $csvUrls[$table] ?? '';

if (empty($csvUrl)) {
    echo json_encode(['success' => false, 'message' => 'URL CSV no configurada para esta tabla. Configure en la sección de Sincronización.']);
    exit;
}

try {
    // Descargar el CSV desde Google Sheets
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $csvUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $csvData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        throw new Exception("Error al descargar CSV. Código HTTP: {$httpCode}");
    }
    
    if (empty($csvData)) {
        throw new Exception("El archivo CSV está vacío o no se pudo descargar");
    }
    
    // Procesar CSV correctamente (maneja campos con saltos de línea)
    $rows = [];
    $handle = fopen('php://temp', 'r+');
    fwrite($handle, $csvData);
    rewind($handle);
    
    while (($row = fgetcsv($handle)) !== false) {
        $rows[] = $row;
    }
    fclose($handle);
    
    if (empty($rows)) {
        throw new Exception("No se encontraron datos en el CSV");
    }
    
    // Conectar a la base de datos
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception("No se pudo conectar a la base de datos");
    }
    
    // Primera fila como encabezados
    $headers = array_shift($rows);
    
    // Limpiar encabezados (remover BOM y espacios)
    $headers = array_map(function($header) {
        return trim(str_replace("\xEF\xBB\xBF", '', $header));
    }, $headers);
    
    $processed = 0;
    $successful = 0;
    $failed = 0;
    $errors = [];
    
    // Limpiar tabla antes de insertar (opcional - descomentar si quieres limpiar cada vez)
    // pg_query($conn, "TRUNCATE TABLE " . pg_escape_identifier($conn, $table) . " RESTART IDENTITY CASCADE");
    
    foreach ($rows as $rowIndex => $row) {
        // Saltar filas vacías
        if (empty($row) || (count($row) === 1 && empty($row[0]))) {
            continue;
        }
        
        // Verificar que la fila tenga datos válidos
        if (count($row) < count($headers)) {
            // Rellenar con valores vacíos si faltan columnas
            $row = array_pad($row, count($headers), '');
        }
        
        $processed++;
        
        try {
            // Mapear datos según la tabla
            $data = array_combine($headers, $row);
            
            // Preparar query de inserción según la tabla
            $inserted = insertDataByTable($conn, $table, $data);
            
            if ($inserted) {
                $successful++;
            } else {
                $failed++;
                $errors[] = "Fila " . ($rowIndex + 1) . ": Error al insertar";
            }
            
        } catch (Exception $e) {
            $failed++;
            $errors[] = "Fila " . ($rowIndex + 1) . ": " . $e->getMessage();
            error_log("Error procesando fila " . ($rowIndex + 1) . ": " . $e->getMessage());
        }
    }
    
    // Registrar en sync_log
    $logQuery = "INSERT INTO sync_log (tabla_nombre, registros_procesados, registros_exitosos, registros_fallidos, estado, mensaje) 
                 VALUES ($1, $2, $3, $4, $5, $6)";
    $estado = ($failed === 0) ? 'completado' : 'completado_con_errores';
    $mensaje = "Sincronización desde Google Sheets";
    
    pg_query_params($conn, $logQuery, [
        $table, 
        $processed, 
        $successful, 
        $failed, 
        $estado, 
        $mensaje
    ]);
    
    pg_close($conn);
    
    $response = [
        'success' => true,
        'processed' => $processed,
        'successful' => $successful,
        'failed' => $failed,
        'message' => 'Sincronización completada'
    ];
    
    // Agregar errores si los hay (solo primeros 5)
    if (!empty($errors)) {
        $response['errors'] = array_slice($errors, 0, 5);
        $response['message'] .= ' (con ' . count($errors) . ' errores)';
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Función para insertar datos según la tabla
function insertDataByTable($conn, $table, $data) {
    switch ($table) {
        case 'campañas':
            $query = "INSERT INTO campañas (nombre, descripcion, fecha_inicio, fecha_fin, estado, puntos_base) 
                      VALUES ($1, $2, $3, $4, $5, $6)
                      ON CONFLICT DO NOTHING";
            return pg_query_params($conn, $query, [
                $data['nombre'] ?? '',
                $data['descripcion'] ?? '',
                $data['fecha_inicio'] ?? null,
                $data['fecha_fin'] ?? null,
                $data['estado'] ?? 'activa',
                $data['puntos_base'] ?? 0
            ]);
            
        case 'ingles':
            $query = "INSERT INTO ingles (empleado_id, empleado_nombre, nivel, puntos, fecha_evaluacion, certificacion) 
                      VALUES ($1, $2, $3, $4, $5, $6)
                      ON CONFLICT DO NOTHING";
            return pg_query_params($conn, $query, [
                $data['empleado_id'] ?? null,
                $data['empleado_nombre'] ?? '',
                $data['nivel'] ?? '',
                $data['puntos'] ?? 0,
                $data['fecha_evaluacion'] ?? null,
                $data['certificacion'] ?? ''
            ]);
            
        case 'pausas':
            // Mapeo de columnas del CSV de Google Sheets a la base de datos
            // CSV: Nombre de la persona, Agrega tu número de identificación, 
            //      Fecha de la Jikkopausas (opcional), Cuantas Jikkopausas hiciste hoy, 
            //      Adjunta tu evidencia 📸📸 (opcional), Escoger la empresa, 
            //      Enviado por, Marca de tiempo
            // BD: empleado_id, empleado_nombre, tipo_pausa, fecha, observaciones
            
            $empleadoId = $data['Agrega tu número de identificación'] ?? $data['empleado_id'] ?? null;
            $empleadoNombre = $data['Nombre de la persona'] ?? $data['empleado_nombre'] ?? '';
            $tipoPausa = $data['Cuantas Jikkopausas hiciste hoy'] ?? $data['tipo_pausa'] ?? '';
            $evidencia = $data['Adjunta tu evidencia 📸📸'] ?? '';
            $empresa = $data['Escoger la empresa'] ?? '';
            $enviadoPor = $data['Enviado por'] ?? '';
            $marcaTiempo = $data['Marca de tiempo'] ?? null;
            
            // Preferir fecha explícita del CSV si existe, sino extraer de marca de tiempo
            $fecha = $data['Fecha de la Jikkopausas'] ?? null;
            
            if (!$fecha && $marcaTiempo) {
                // Extraer fecha de la marca de tiempo (formato: "29 ene 2026 15:30:01")
                try {
                    $dt = new DateTime($marcaTiempo);
                    $fecha = $dt->format('Y-m-d');
                } catch (Exception $e) {
                    // Si falla, intentar extraer manualmente
                    if (preg_match('/(\d{1,2})\s+([a-z]{3})\s+(\d{4})/i', $marcaTiempo, $matches)) {
                        $meses = [
                            'ene' => '01', 'feb' => '02', 'mar' => '03', 'abr' => '04',
                            'may' => '05', 'jun' => '06', 'jul' => '07', 'ago' => '08',
                            'sep' => '09', 'oct' => '10', 'nov' => '11', 'dic' => '12'
                        ];
                        $dia = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                        $mes = $meses[strtolower($matches[2])] ?? '01';
                        $ano = $matches[3];
                        $fecha = "$ano-$mes-$dia";
                    }
                }
            } elseif ($fecha) {
                // Convertir fecha si está en formato dd/mm/yyyy o similar
                if (strpos($fecha, '/') !== false) {
                    $fechaParts = explode('/', $fecha);
                    if (count($fechaParts) === 3) {
                        $fecha = $fechaParts[2] . '-' . $fechaParts[1] . '-' . $fechaParts[0];
                    }
                }
            }
            
            // Limpiar nombre de empleado (remover @ del inicio si existe)
            $empleadoNombre = ltrim($empleadoNombre, '@');
            
            // Combinar información adicional en observaciones
            $observaciones = [];
            if ($evidencia) $observaciones[] = "Evidencia: $evidencia";
            if ($empresa) $observaciones[] = "Empresa: $empresa";
            if ($enviadoPor) $observaciones[] = "Enviado por: $enviadoPor";
            $obsText = implode(' | ', $observaciones);
            
            $query = "INSERT INTO pausas (empleado_id, empleado_nombre, tipo_pausa, fecha, observaciones) 
                      VALUES ($1, $2, $3, $4, $5)
                      ON CONFLICT DO NOTHING";
            return pg_query_params($conn, $query, [
                $empleadoId,
                trim($empleadoNombre),
                trim($tipoPausa),
                $fecha,
                $obsText
            ]);
            
        case 'puntos_adicionales':
            // Mapeo de columnas del CSV de Google Sheets a la base de datos
            // CSV: Documento, Fecha, Nombre, Actividad, # de puntos, Responsable
            // BD: empleado_id, empleado_nombre, concepto, puntos, fecha, aprobado_por
            
            $empleadoId = $data['Documento'] ?? $data['empleado_id'] ?? null;
            $empleadoNombre = $data['Nombre'] ?? $data['empleado_nombre'] ?? '';
            $concepto = $data['Actividad'] ?? $data['concepto'] ?? '';
            $puntos = $data['# de puntos'] ?? $data['puntos'] ?? 0;
            $fecha = $data['Fecha'] ?? $data['fecha'] ?? null;
            $aprobadoPor = $data['Responsable'] ?? $data['aprobado_por'] ?? '';
            $observaciones = $data['observaciones'] ?? '';
            
            // Convertir fecha si está en formato dd/mm/yyyy
            if ($fecha && strpos($fecha, '/') !== false) {
                $fechaParts = explode('/', $fecha);
                if (count($fechaParts) === 3) {
                    // Cambiar de dd/mm/yyyy a yyyy-mm-dd
                    $fecha = $fechaParts[2] . '-' . $fechaParts[1] . '-' . $fechaParts[0];
                }
            }
            
            // Limpiar y validar puntos (remover espacios y caracteres no numéricos)
            $puntos = preg_replace('/[^0-9.-]/', '', $puntos);
            if (empty($puntos) || !is_numeric($puntos)) {
                $puntos = 0;
            }
            
            $query = "INSERT INTO puntos_adicionales (empleado_id, empleado_nombre, concepto, puntos, fecha, aprobado_por, observaciones) 
                      VALUES ($1, $2, $3, $4, $5, $6, $7)
                      ON CONFLICT DO NOTHING";
            return pg_query_params($conn, $query, [
                $empleadoId,
                trim($empleadoNombre),
                trim($concepto),
                intval($puntos),
                $fecha,
                trim($aprobadoPor),
                trim($observaciones)
            ]);
            
        default:
            return false;
    }
}
?>
