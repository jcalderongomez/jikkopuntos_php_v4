<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🚀 Cargando Datos de Puntos Adicionales</h1>";
echo "<style>body{font-family:Arial;margin:20px;}.success{color:green;}.error{color:red;}.info{color:blue;}</style>";

// 1. Verificar conexión a PostgreSQL
echo "<h2>1. Verificando conexión a PostgreSQL...</h2>";
require_once '../config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception("No se pudo conectar a la base de datos");
    }
    
    echo "<p class='success'>✓ Conexión exitosa a PostgreSQL</p>";
    
    // 2. Verificar/Crear tablas
    echo "<h2>2. Verificando tablas...</h2>";
    
    $checkTable = pg_query($conn, "SELECT EXISTS (
        SELECT FROM information_schema.tables 
        WHERE table_name = 'puntos_adicionales'
    )");
    
    $tableExists = pg_fetch_result($checkTable, 0, 0) === 't';
    
    if (!$tableExists) {
        echo "<p class='info'>Tabla no existe. Creando...</p>";
        
        $sqlFile = '../database.sql';
        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            $result = pg_query($conn, $sql);
            
            if ($result) {
                echo "<p class='success'>✓ Tablas creadas exitosamente</p>";
            } else {
                throw new Exception("Error al crear tablas: " . pg_last_error($conn));
            }
        } else {
            throw new Exception("Archivo database.sql no encontrado");
        }
    } else {
        echo "<p class='success'>✓ Tabla 'puntos_adicionales' existe</p>";
    }
    
    // 3. Descargar CSV
    echo "<h2>3. Descargando CSV desde Google Sheets...</h2>";
    
    $csvUrl = "https://docs.google.com/spreadsheets/d/e/2PACX-1vT_YovS-kYOHIkiwx_YQyzMvixS52UQSihicIpKL0mv3Z2QZZShLLk-NnrANoQIKE7ZcbbWdxO40lQa/pub?gid=0&single=true&output=csv";
    
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
    
    echo "<p class='success'>✓ CSV descargado (" . strlen($csvData) . " bytes)</p>";
    
    // 4. Procesar y cargar datos
    echo "<h2>4. Procesando y cargando datos...</h2>";
    
    // Procesar CSV correctamente (maneja campos con saltos de línea)
    $rows = [];
    $handle = fopen('php://temp', 'r+');
    fwrite($handle, $csvData);
    rewind($handle);
    
    while (($row = fgetcsv($handle)) !== false) {
        $rows[] = $row;
    }
    fclose($handle);
    
    $headers = array_shift($rows);
    
    // Limpiar encabezados
    $headers = array_map(function($h) {
        return trim(str_replace("\xEF\xBB\xBF", '', $h));
    }, $headers);
    
    echo "<p class='info'>Encabezados: " . implode(', ', $headers) . "</p>";
    
    $processed = 0;
    $successful = 0;
    $failed = 0;
    $errors = [];
    
    // Limpiar tabla antes de cargar
    pg_query($conn, "TRUNCATE TABLE puntos_adicionales RESTART IDENTITY CASCADE");
    echo "<p class='info'>Tabla limpiada. Insertando nuevos registros...</p>";
    
    foreach ($rows as $rowIndex => $row) {
        // Saltar filas vacías
        if (empty($row) || (count($row) === 1 && empty($row[0]))) {
            continue;
        }
        
        if (count($row) < count($headers)) {
            $row = array_pad($row, count($headers), '');
        }
        
        $processed++;
        
        try {
            $data = array_combine($headers, $row);
            
            $empleadoId = $data['Documento'] ?? null;
            $empleadoNombre = $data['Nombre'] ?? '';
            $concepto = $data['Actividad'] ?? '';
            $puntos = $data['# de puntos'] ?? 0;
            $fecha = $data['Fecha'] ?? null;
            $aprobadoPor = $data['Responsable'] ?? '';
            
            // Convertir fecha si está en formato dd/mm/yyyy
            if ($fecha && strpos($fecha, '/') !== false) {
                $fechaParts = explode('/', $fecha);
                if (count($fechaParts) === 3) {
                    $fecha = $fechaParts[2] . '-' . $fechaParts[1] . '-' . $fechaParts[0];
                }
            }
            
            // Limpiar puntos
            $puntos = preg_replace('/[^0-9.-]/', '', $puntos);
            if (empty($puntos) || !is_numeric($puntos)) {
                $puntos = 0;
            }
            
            $query = "INSERT INTO puntos_adicionales (empleado_id, empleado_nombre, concepto, puntos, fecha, aprobado_por, observaciones) 
                      VALUES ($1, $2, $3, $4, $5, $6, $7)";
            
            $result = pg_query_params($conn, $query, [
                $empleadoId,
                trim($empleadoNombre),
                trim($concepto),
                intval($puntos),
                $fecha,
                trim($aprobadoPor),
                ''
            ]);
            
            if ($result) {
                $successful++;
            } else {
                $failed++;
                $errors[] = "Fila $processed: " . pg_last_error($conn);
            }
            
        } catch (Exception $e) {
            $failed++;
            $errors[] = "Fila $processed: " . $e->getMessage();
        }
    }
    
    // 5. Resumen
    echo "<h2>5. Resumen de Carga</h2>";
    echo "<p class='success'><strong>✓ Carga completada</strong></p>";
    echo "<ul>";
    echo "<li><strong>Filas procesadas:</strong> $processed</li>";
    echo "<li><strong>Exitosas:</strong> <span class='success'>$successful</span></li>";
    echo "<li><strong>Fallidas:</strong> <span class='error'>$failed</span></li>";
    echo "</ul>";
    
    if (!empty($errors)) {
        echo "<h3>Errores (primeros 10):</h3>";
        echo "<ul class='error'>";
        foreach (array_slice($errors, 0, 10) as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul>";
    }
    
    // Mostrar algunos registros
    echo "<h2>6. Primeros 10 registros en la base de datos</h2>";
    $result = pg_query($conn, "SELECT * FROM puntos_adicionales ORDER BY id ASC LIMIT 10");
    
    echo "<table border='1' cellpadding='10' style='border-collapse:collapse;width:100%;'>";
    echo "<tr style='background:#333;color:white;'>";
    echo "<th>ID</th><th>Documento</th><th>Nombre</th><th>Actividad</th><th>Puntos</th><th>Fecha</th><th>Responsable</th>";
    echo "</tr>";
    
    while ($row = pg_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['empleado_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['empleado_nombre']) . "</td>";
        echo "<td>" . htmlspecialchars(substr($row['concepto'], 0, 50)) . "...</td>";
        echo "<td><strong>" . htmlspecialchars($row['puntos']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($row['fecha']) . "</td>";
        echo "<td>" . htmlspecialchars($row['aprobado_por']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    pg_close($conn);
    
    echo "<hr>";
    echo "<h2 class='success'>🎉 ¡Proceso completado exitosamente!</h2>";
    echo "<p><a href='../index.php' style='padding:10px 20px;background:#4F46E5;color:white;text-decoration:none;border-radius:5px;'>← Volver al Dashboard</a></p>";
    
} catch (Exception $e) {
    echo "<p class='error'><strong>✗ ERROR:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><a href='diagnostico.php'>Ejecutar Diagnóstico Completo</a></p>";
}
?>
