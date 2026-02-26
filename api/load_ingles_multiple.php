<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$urls = [
    'https://docs.google.com/spreadsheets/d/e/2PACX-1vRMZtKS4lyiGbHjEKVL54fH6B44IJ9nGbHGM1mhPoatjB1Fpz-glBHKU8GHvvrWUzP9_K-W07s0UOer/pub?gid=114180797&single=true&output=csv'
];

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Limpiar tabla antes de cargar
    $truncateQuery = "TRUNCATE TABLE ingles RESTART IDENTITY CASCADE";
    pg_query($conn, $truncateQuery);
    
    $totalProcessed = 0;
    $totalSuccessful = 0;
    $totalFailed = 0;
    $errors = [];
    $firstRowColumns = null;
    
    foreach ($urls as $index => $url) {
        // echo "Procesando fuente " . ($index + 1) . "...\n";
        
        $csvContent = file_get_contents($url);
        if ($csvContent === false) {
            $errors[] = "No se pudo obtener el contenido de la fuente " . ($index + 1);
            continue;
        }
        
        // Crear archivo temporal
        $tempFile = tempnam(sys_get_temp_dir(), 'csv');
        file_put_contents($tempFile, $csvContent);
        
        $handle = fopen($tempFile, 'r');
        if ($handle === false) {
            $errors[] = "No se pudo abrir el archivo temporal de la fuente " . ($index + 1);
            unlink($tempFile);
            continue;
        }
        
        // Leer encabezados
        $headers = fgetcsv($handle);
        if ($headers === false) {
            $errors[] = "No se pudieron leer los encabezados de la fuente " . ($index + 1);
            fclose($handle);
            unlink($tempFile);
            continue;
        }
        
        // Guardar columnas de la primera fila para debug
        if ($firstRowColumns === null) {
            $firstRowColumns = $headers;
            // echo "\n=== COLUMNAS DETECTADAS EN LA PRIMERA FUENTE ===\n";
            // foreach ($headers as $idx => $col) {
            //     echo "Columna $idx: '$col'\n";
            // }
            // echo "================================================\n\n";
        }
        
        $rowNumber = 1;
        
        while (($data = fgetcsv($handle)) !== false) {
            $rowNumber++;
            $totalProcessed++;
            
            // Crear array asociativo
            $row = array_combine($headers, $data);
            
            // Mapeo flexible de columnas para múltiples formatos
            $empleadoId = $row['NÚMERO DOCUMENTO DE IDENTIDAD']
                ?? $row['Número de identificación'] 
                ?? $row['Numero de identificación']
                ?? $row['Documento']
                ?? $row['Agrega tu número de identificación']
                ?? null;
                
            $empleadoNombre = $row['NOMBRES Y APELLIDOS']
                ?? $row['Nombre de la persona'] 
                ?? $row['Nombre'] 
                ?? $row['Nombre completo']
                ?? null;
                
            $nivelIngles = $row['Nivel de inglés'] 
                ?? $row['Nivel de Inglés']
                ?? $row['¿Cuál es tu nivel de inglés?']
                ?? $row['Nivel']
                ?? null;
                
            $fecha = $row['Fecha en la que asistí al Conversation Club / English Class']
                ?? $row['Fecha'] 
                ?? $row['Marca temporal']
                ?? $row['Marca de tiempo']
                ?? $row['Timestamp']
                ?? null;
                
            $observaciones = $row['Observaciones'] 
                ?? $row['Comentarios']
                ?? $row['Notas']
                ?? '';
            
            // Validar campos requeridos
            if (empty($empleadoId) || empty($empleadoNombre)) {
                $errors[] = "Fila $rowNumber de fuente " . ($index + 1) . ": Faltan campos requeridos (ID o Nombre)";
                $totalFailed++;
                continue;
            }
            
            // Convertir fecha
            $fechaFormateada = null;
            if (!empty($fecha)) {
                $fechaFormateada = convertirFecha($fecha);
            }
            
            // Insertar en la base de datos (30 puntos por asistencia)
            $query = "INSERT INTO ingles (empleado_id, empleado_nombre, nivel, puntos, fecha_evaluacion, certificacion) 
                      VALUES ($1, $2, $3, $4, $5, $6)";
            
            $result = pg_query_params($conn, $query, [
                trim($empleadoId),
                trim($empleadoNombre),
                $nivelIngles ? trim($nivelIngles) : null,
                30, // 30 puntos por asistencia
                $fechaFormateada,
                trim($observaciones)
            ]);
            
            if ($result) {
                $totalSuccessful++;
            } else {
                $totalFailed++;
                $errors[] = "Fila $rowNumber de fuente " . ($index + 1) . ": " . pg_last_error($conn);
            }
        }
        
        fclose($handle);
        unlink($tempFile);
        
        // echo "Fuente " . ($index + 1) . " procesada.\n";
    }
    
    pg_close($conn);
    
    // Respuesta JSON
    echo json_encode([
        'success' => true,
        'processed' => $totalProcessed,
        'successful' => $totalSuccessful,
        'failed' => $totalFailed,
        'errors' => array_slice($errors, 0, 10),
        'columns_detected' => $firstRowColumns
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

function convertirFecha($fecha) {
    if (empty($fecha)) return null;
    
    // Si ya está en formato YYYY-MM-DD
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        return $fecha;
    }
    
    // Si está en formato DD/MM/YYYY HH:MM:SS (Google Forms timestamp)
    if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})\s+\d{2}:\d{2}:\d{2}$/', $fecha, $matches)) {
        return sprintf('%04d-%02d-%02d', $matches[3], $matches[2], $matches[1]);
    }
    
    // Si está en formato DD/MM/YYYY
    if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $fecha, $matches)) {
        return sprintf('%04d-%02d-%02d', $matches[3], $matches[2], $matches[1]);
    }
    
    // Si está en formato M/D/YYYY
    if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $fecha, $matches)) {
        return sprintf('%04d-%02d-%02d', $matches[3], $matches[1], $matches[2]);
    }
    
    // Intentar parsearlo con strtotime
    $timestamp = strtotime($fecha);
    if ($timestamp !== false) {
        return date('Y-m-d', $timestamp);
    }
    
    return null;
}
