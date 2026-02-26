<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

// URLs de los CSV de pausas
$urls = [
    'https://docs.google.com/spreadsheets/d/e/2PACX-1vS8Lo9tq18jV_YjjXxkyZuiH3KJYX7DWlVXQ3PSa7q9tle9PbnDbc4zOGD-8-T3oMy7giLw-9G05mz-/pub?gid=1986324007&single=true&output=csv',
    'https://docs.google.com/spreadsheets/d/e/2PACX-1vQPPr7ViEJvdYFeBlIADH7vdtDGqQ_mMcKvXkGaAMer7OCZYOg63oFfacJtXLEr-h1d0PQHbPWSSAmS/pub?gid=2009879901&single=true&output=csv',
    'https://docs.google.com/spreadsheets/d/e/2PACX-1vQS-_lwEc-HOXTKNZEngA9iscVMMEZJUSuYiaKgsaqyHlPc_C8Mcr8ztrBt9Ybm3Xsls0KvvT0J94hu/pub?gid=1498108368&single=true&output=csv'
];

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception("No se pudo conectar a la base de datos");
    }
    
    // Limpiar tabla antes de cargar
    pg_query($conn, "TRUNCATE TABLE pausas RESTART IDENTITY CASCADE");
    
    $totalProcessed = 0;
    $totalSuccessful = 0;
    $totalFailed = 0;
    $errors = [];
    
    foreach ($urls as $urlIndex => $url) {
        // Descargar CSV
        $csvData = @file_get_contents($url);
        
        if (!$csvData) {
            $errors[] = "Error descargando CSV " . ($urlIndex + 1);
            continue;
        }
        
        // Procesar CSV usando fgetcsv
        $handle = fopen('php://temp', 'r+');
        fwrite($handle, $csvData);
        rewind($handle);
        
        $rows = [];
        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = $row;
        }
        fclose($handle);
        
        if (empty($rows)) {
            $errors[] = "CSV " . ($urlIndex + 1) . " vacío";
            continue;
        }
        
        // Primera fila como encabezados
        $headers = array_shift($rows);
        
        // Limpiar encabezados
        $headers = array_map(function($header) {
            return trim(str_replace("\xEF\xBB\xBF", '', $header));
        }, $headers);
        
        foreach ($rows as $rowIndex => $row) {
            // Saltar filas vacías
            if (empty($row) || (count($row) === 1 && empty($row[0]))) {
                continue;
            }
            
            // Verificar que la fila tenga datos válidos
            if (count($row) < count($headers)) {
                $row = array_pad($row, count($headers), '');
            }
            
            $totalProcessed++;
            
            try {
                $data = array_combine($headers, $row);
                
                // Mapear datos - soporta múltiples nombres de columnas
                $empleadoId = $data['Agrega tu número de identificación'] 
                    ?? $data['Número de identificación'] 
                    ?? null;
                    
                $empleadoNombre = $data['Nombre de la persona'] 
                    ?? $data['Nombre'] 
                    ?? '';
                    
                $tipoPausa = $data['Cuantas Jikkopausas hiciste hoy'] 
                    ?? $data['¿Cuántas Jikko- Pausas hiciste hoy? (La fecha que registraste en la pregunta anterior)'] 
                    ?? '';
                    
                $evidencia = $data['Adjunta tu evidencia 📸📸'] ?? '';
                
                $empresa = $data['Escoger la empresa'] 
                    ?? $data['Empresa'] 
                    ?? '';
                    
                $enviadoPor = $data['Enviado por'] ?? '';
                
                $correo = $data['Dirección de correo electrónico'] ?? '';
                
                $marcaTiempo = $data['Marca de tiempo'] 
                    ?? $data['Marca temporal'] 
                    ?? null;
                
                // Preferir fecha explícita del CSV si existe (soporta múltiples nombres)
                $fecha = $data['Fecha de la Jikkopausas'] 
                    ?? $data['¿Fecha en que hiciste la Jikko Pausa? (en caso que hayas olvidado tu registro el día anterior)'] 
                    ?? null;
                
                if (!$fecha && $marcaTiempo) {
                    // Extraer fecha de marca de tiempo
                    try {
                        $dt = new DateTime($marcaTiempo);
                        $fecha = $dt->format('Y-m-d');
                    } catch (Exception $e) {
                        // Intentar formato español: "29 ene 2026 15:30:01"
                        if (preg_match('/(\d{1,2})\s+([a-z]{3})\s+(\d{4})/i', $marcaTiempo, $matches)) {
                            $meses = [
                                'ene' => '01', 'jan' => '01', 'feb' => '02', 'mar' => '03', 'abr' => '04', 'apr' => '04',
                                'may' => '05', 'jun' => '06', 'jul' => '07', 'ago' => '08', 'aug' => '08',
                                'sep' => '09', 'oct' => '10', 'nov' => '11', 'dic' => '12', 'dec' => '12'
                            ];
                            $dia = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                            $mes = $meses[strtolower($matches[2])] ?? '01';
                            $ano = $matches[3];
                            $fecha = "$ano-$mes-$dia";
                        }
                    }
                } elseif ($fecha) {
                    // Convertir fecha si está en formato dd/mm/yyyy
                    if (strpos($fecha, '/') !== false) {
                        $fechaParts = explode('/', $fecha);
                        if (count($fechaParts) === 3) {
                            $fecha = $fechaParts[2] . '-' . $fechaParts[1] . '-' . $fechaParts[0];
                        }
                    }
                }
                
                // Limpiar nombre
                $empleadoNombre = ltrim(trim($empleadoNombre), '@');
                
                // Combinar observaciones
                $observaciones = [];
                if ($evidencia) $observaciones[] = "Evidencia: $evidencia";
                if ($empresa) $observaciones[] = "Empresa: $empresa";
                if ($enviadoPor) $observaciones[] = "Enviado por: $enviadoPor";
                if ($correo) $observaciones[] = "Email: $correo";
                $obsText = implode(' | ', $observaciones);
                
                $query = "INSERT INTO pausas (empleado_id, empleado_nombre, tipo_pausa, fecha, observaciones) 
                          VALUES ($1, $2, $3, $4, $5)";
                $result = pg_query_params($conn, $query, [
                    $empleadoId,
                    trim($empleadoNombre),
                    trim($tipoPausa),
                    $fecha,
                    $obsText
                ]);
                
                if ($result) {
                    $totalSuccessful++;
                } else {
                    $totalFailed++;
                    $errors[] = "CSV " . ($urlIndex + 1) . " fila " . ($rowIndex + 1) . ": " . pg_last_error($conn);
                }
                
            } catch (Exception $e) {
                $totalFailed++;
                $errors[] = "CSV " . ($urlIndex + 1) . " fila " . ($rowIndex + 1) . ": " . $e->getMessage();
            }
        }
    }
    
    // Registrar sincronización
    $logQuery = "INSERT INTO sync_log (tabla_nombre, registros_procesados, registros_exitosos, registros_fallidos, mensaje) 
                 VALUES ($1, $2, $3, $4, $5)";
    pg_query_params($conn, $logQuery, [
        'pausas',
        $totalProcessed,
        $totalSuccessful,
        $totalFailed,
        'Carga desde múltiples CSV'
    ]);
    
    pg_close($conn);
    
    echo json_encode([
        'success' => true,
        'message' => "Datos cargados exitosamente desde " . count($urls) . " fuentes",
        'processed' => $totalProcessed,
        'successful' => $totalSuccessful,
        'failed' => $totalFailed,
        'errors' => array_slice($errors, 0, 10)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
