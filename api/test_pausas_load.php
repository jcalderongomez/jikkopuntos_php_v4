<?php
require_once __DIR__ . '/../config/database.php';

$url = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vS8Lo9tq18jV_YjjXxkyZuiH3KJYX7DWlVXQ3PSa7q9tle9PbnDbc4zOGD-8-T3oMy7giLw-9G05mz-/pub?gid=1986324007&single=true&output=csv';

echo "=== DESCARGANDO CSV DE PAUSAS ===\n\n";

$csvData = file_get_contents($url);
if (!$csvData) {
    die("Error: No se pudo descargar el CSV\n");
}

echo "✓ CSV descargado (" . strlen($csvData) . " bytes)\n\n";

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
    die("Error: No se encontraron datos en el CSV\n");
}

// Primera fila como encabezados
$headers = array_shift($rows);
echo "=== ENCABEZADOS DEL CSV ===\n";
foreach ($headers as $i => $header) {
    echo "$i: $header\n";
}

echo "\n=== PRIMERAS 3 FILAS DE DATOS ===\n\n";
for ($i = 0; $i < min(3, count($rows)); $i++) {
    echo "Fila " . ($i + 1) . ":\n";
    foreach ($headers as $j => $header) {
        echo "  $header: " . ($rows[$i][$j] ?? '') . "\n";
    }
    echo "\n";
}

// Conectar a BD y cargar datos
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("Error: No se pudo conectar a la base de datos\n");
}

// Limpiar tabla
pg_query($conn, "TRUNCATE TABLE pausas RESTART IDENTITY CASCADE");
echo "✓ Tabla pausas limpiada\n\n";

$processed = 0;
$successful = 0;
$failed = 0;

foreach ($rows as $rowIndex => $row) {
    if (empty($row) || (count($row) === 1 && empty($row[0]))) {
        continue;
    }
    
    $processed++;
    
    // Mapear datos
    $data = array_combine($headers, $row);
    
    // Incluir lógica para insertar en pausas usando el mismo código de sync_google_sheets.php
    $empleadoId = $data['Agrega tu número de identificación'] ?? null;
    $empleadoNombre = $data['Nombre de la persona'] ?? '';
    $tipoPausa = $data['Cuantas Jikkopausas hiciste hoy'] ?? '';
    $evidencia = $data['Adjunta tu evidencia 📸📸'] ?? '';
    $empresa = $data['Escoger la empresa'] ?? '';
    $enviadoPor = $data['Enviado por'] ?? '';
    $marcaTiempo = $data['Marca de tiempo'] ?? null;
    
    // Preferir fecha explícita del CSV si existe, sino extraer de marca de tiempo
    $fecha = $data['Fecha de la Jikkopausas'] ?? null;
    
    if (!$fecha && $marcaTiempo) {
        // Extraer fecha de la marca de tiempo
        try {
            $dt = new DateTime($marcaTiempo);
            $fecha = $dt->format('Y-m-d');
        } catch (Exception $e) {
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
    
    // Limpiar nombre
    $empleadoNombre = ltrim($empleadoNombre, '@');
    
    // Combinar observaciones
    $observaciones = [];
    if ($evidencia) $observaciones[] = "Evidencia: $evidencia";
    if ($empresa) $observaciones[] = "Empresa: $empresa";
    if ($enviadoPor) $observaciones[] = "Enviado por: $enviadoPor";
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
        $successful++;
    } else {
        $failed++;
        echo "Error en fila " . ($rowIndex + 1) . ": " . pg_last_error($conn) . "\n";
    }
}

echo "\n=== RESUMEN ===\n";
echo "Procesadas: $processed\n";
echo "Exitosas: $successful\n";
echo "Fallidas: $failed\n";

// Mostrar primeros 5 registros
echo "\n=== PRIMEROS 5 REGISTROS EN BD ===\n\n";
$result = pg_query($conn, "SELECT * FROM pausas ORDER BY id ASC LIMIT 5");
while ($row = pg_fetch_assoc($result)) {
    echo "ID: {$row['id']}\n";
    echo "Documento: {$row['empleado_id']}\n";
    echo "Nombre: {$row['empleado_nombre']}\n";
    echo "Tipo Pausa: {$row['tipo_pausa']}\n";
    echo "Fecha: {$row['fecha']}\n";
    echo "Observaciones: " . substr($row['observaciones'], 0, 80) . "...\n";
    echo "---\n";
}

pg_close($conn);
?>
