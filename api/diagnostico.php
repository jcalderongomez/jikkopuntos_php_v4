<?php
// Script de diagnóstico completo para puntos adicionales
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Diagnóstico Completo - Puntos Adicionales</h1>";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.success { color: green; font-weight: bold; }
.error { color: red; font-weight: bold; }
.warning { color: orange; font-weight: bold; }
pre { background: #f4f4f4; padding: 15px; overflow-x: auto; border-left: 4px solid #333; }
table { border-collapse: collapse; width: 100%; margin: 20px 0; }
th { background: #333; color: white; padding: 10px; text-align: left; }
td { padding: 8px; border-bottom: 1px solid #ddd; }
.section { margin: 30px 0; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
</style>";

// 1. Verificar configuración
echo "<div class='section'>";
echo "<h2>1. Verificar Configuración</h2>";

$configFile = '../config/google_sheets_config.json';
if (file_exists($configFile)) {
    echo "<p class='success'>✓ Archivo de configuración existe</p>";
    $config = json_decode(file_get_contents($configFile), true);
    echo "<pre>" . json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "</pre>";
    
    $csvUrl = $config['csv_urls']['puntos_adicionales'] ?? '';
    if (!empty($csvUrl)) {
        echo "<p class='success'>✓ URL CSV configurada para puntos_adicionales</p>";
        echo "<p><strong>URL:</strong> " . htmlspecialchars($csvUrl) . "</p>";
    } else {
        echo "<p class='error'>✗ URL CSV NO configurada para puntos_adicionales</p>";
    }
} else {
    echo "<p class='error'>✗ Archivo de configuración NO existe</p>";
}
echo "</div>";

// 2. Probar descarga del CSV
echo "<div class='section'>";
echo "<h2>2. Descargar y Analizar CSV</h2>";

if (!empty($csvUrl)) {
    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $csvUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $csvData = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "<p><strong>Código HTTP:</strong> $httpCode</p>";
        
        if ($httpCode === 200) {
            echo "<p class='success'>✓ CSV descargado exitosamente</p>";
            echo "<p><strong>Tamaño:</strong> " . strlen($csvData) . " bytes</p>";
            
            // Procesar CSV
            $rows = array_map('str_getcsv', explode("\n", trim($csvData)));
            $headers = array_shift($rows);
            
            // Limpiar encabezados
            $headers = array_map(function($h) {
                return trim(str_replace("\xEF\xBB\xBF", '', $h));
            }, $headers);
            
            echo "<p class='success'>✓ " . count($rows) . " filas de datos encontradas</p>";
            
            echo "<h3>Encabezados del CSV:</h3>";
            echo "<ul>";
            foreach ($headers as $i => $header) {
                echo "<li><strong>[$i]</strong> " . htmlspecialchars($header) . "</li>";
            }
            echo "</ul>";
            
            echo "<h3>Primeras 10 filas:</h3>";
            echo "<table border='1'>";
            echo "<thead><tr>";
            foreach ($headers as $header) {
                echo "<th>" . htmlspecialchars($header) . "</th>";
            }
            echo "</tr></thead><tbody>";
            
            $rowCount = 0;
            foreach ($rows as $row) {
                // Saltar filas vacías
                if (empty($row) || (count($row) === 1 && empty($row[0]))) {
                    continue;
                }
                
                if (count($row) < count($headers)) {
                    $row = array_pad($row, count($headers), '');
                }
                
                echo "<tr>";
                foreach ($row as $cell) {
                    echo "<td>" . htmlspecialchars($cell) . "</td>";
                }
                echo "</tr>";
                
                $rowCount++;
                if ($rowCount >= 10) break;
            }
            echo "</tbody></table>";
            
        } else {
            echo "<p class='error'>✗ Error al descargar CSV. Código: $httpCode</p>";
        }
        
    } catch (Exception $e) {
        echo "<p class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p class='warning'>⚠ No se puede probar descarga sin URL configurada</p>";
}
echo "</div>";

// 3. Verificar conexión a PostgreSQL
echo "<div class='section'>";
echo "<h2>3. Verificar Conexión PostgreSQL</h2>";

require_once '../config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn) {
        echo "<p class='success'>✓ Conexión a PostgreSQL exitosa</p>";
        
        // Verificar si existe la tabla
        $tableCheck = pg_query($conn, "SELECT EXISTS (
            SELECT FROM information_schema.tables 
            WHERE table_name = 'puntos_adicionales'
        )");
        
        $exists = pg_fetch_result($tableCheck, 0, 0);
        
        if ($exists === 't') {
            echo "<p class='success'>✓ Tabla 'puntos_adicionales' existe</p>";
            
            // Contar registros
            $countResult = pg_query($conn, "SELECT COUNT(*) FROM puntos_adicionales");
            $count = pg_fetch_result($countResult, 0, 0);
            echo "<p><strong>Registros actuales en la tabla:</strong> $count</p>";
            
            // Mostrar estructura de la tabla
            $structureQuery = "SELECT column_name, data_type, is_nullable 
                              FROM information_schema.columns 
                              WHERE table_name = 'puntos_adicionales' 
                              ORDER BY ordinal_position";
            $structureResult = pg_query($conn, $structureQuery);
            
            echo "<h3>Estructura de la tabla:</h3>";
            echo "<table border='1'>";
            echo "<tr><th>Columna</th><th>Tipo</th><th>Nullable</th></tr>";
            while ($col = pg_fetch_assoc($structureResult)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($col['column_name']) . "</td>";
                echo "<td>" . htmlspecialchars($col['data_type']) . "</td>";
                echo "<td>" . htmlspecialchars($col['is_nullable']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Mostrar últimos 5 registros
            if ($count > 0) {
                echo "<h3>Últimos 5 registros:</h3>";
                $dataResult = pg_query($conn, "SELECT * FROM puntos_adicionales ORDER BY id ASC LIMIT 5");
                
                echo "<table border='1'>";
                echo "<tr><th>ID</th><th>Empleado</th><th>Concepto</th><th>Puntos</th><th>Fecha</th></tr>";
                while ($row = pg_fetch_assoc($dataResult)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['empleado_nombre']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['concepto']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['puntos']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['fecha']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
            
        } else {
            echo "<p class='error'>✗ Tabla 'puntos_adicionales' NO existe</p>";
            echo "<p>Ejecute el script database.sql para crear las tablas</p>";
        }
        
        pg_close($conn);
    } else {
        echo "<p class='error'>✗ No se pudo conectar a PostgreSQL</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
echo "</div>";

// 4. Mapeo de encabezados
echo "<div class='section'>";
echo "<h2>4. Verificar Mapeo de Encabezados</h2>";

$expectedHeaders = ['empleado_id', 'empleado_nombre', 'concepto', 'puntos', 'fecha', 'aprobado_por', 'observaciones'];

echo "<p><strong>Encabezados esperados en el CSV:</strong></p>";
echo "<ul>";
foreach ($expectedHeaders as $header) {
    echo "<li>" . htmlspecialchars($header) . "</li>";
}
echo "</ul>";

if (isset($headers)) {
    echo "<p><strong>Comparación:</strong></p>";
    echo "<table border='1'>";
    echo "<tr><th>Esperado</th><th>En CSV</th><th>Estado</th></tr>";
    foreach ($expectedHeaders as $expected) {
        $found = in_array($expected, $headers);
        echo "<tr>";
        echo "<td>" . htmlspecialchars($expected) . "</td>";
        echo "<td>" . ($found ? htmlspecialchars($expected) : '-') . "</td>";
        echo "<td class='" . ($found ? 'success' : 'error') . "'>" . ($found ? '✓' : '✗') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}
echo "</div>";

// 5. Resumen
echo "<div class='section'>";
echo "<h2>5. Resumen y Recomendaciones</h2>";
echo "<ol>";
echo "<li>Si todos los checks están en verde, el sistema debería funcionar correctamente</li>";
echo "<li>Si hay errores en la configuración, vaya a la sección 'Sincronizar Google Sheets' en el dashboard</li>";
echo "<li>Si hay errores en PostgreSQL, ejecute el script database.sql</li>";
echo "<li>Si los encabezados no coinciden, ajuste su Google Sheet</li>";
echo "</ol>";
echo "</div>";

echo "<hr>";
echo "<p><a href='../index.php'>← Volver al Dashboard</a> | <a href='test_csv.php'>Ver CSV Simple</a></p>";
?>
