<?php
// Script de prueba para verificar el CSV de Google Sheets
header('Content-Type: text/html; charset=utf-8');

$csvUrl = "https://docs.google.com/spreadsheets/d/e/2PACX-1vT_YovS-kYOHIkiwx_YQyzMvixS52UQSihicIpKL0mv3Z2QZZShLLk-NnrANoQIKE7ZcbbWdxO40lQa/pub?gid=0&single=true&output=csv";

echo "<h2>Prueba de Descarga CSV - Puntos Adicionales</h2>";
echo "<p><strong>URL:</strong> " . htmlspecialchars($csvUrl) . "</p>";
echo "<hr>";

try {
    // Descargar el CSV
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $csvUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $csvData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "<p><strong>Código HTTP:</strong> $httpCode</p>";
    
    if ($httpCode !== 200) {
        throw new Exception("Error al descargar CSV. Código HTTP: {$httpCode}");
    }
    
    if (empty($csvData)) {
        throw new Exception("El archivo CSV está vacío");
    }
    
    echo "<p><strong>Tamaño del archivo:</strong> " . strlen($csvData) . " bytes</p>";
    echo "<hr>";
    
    // Procesar CSV
    $rows = array_map('str_getcsv', explode("\n", trim($csvData)));
    
    echo "<h3>Datos del CSV (Primeras 10 filas):</h3>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    
    $rowCount = 0;
    foreach ($rows as $row) {
        if ($rowCount === 0) {
            echo "<thead><tr style='background-color: #333; color: white;'>";
            foreach ($row as $cell) {
                echo "<th>" . htmlspecialchars($cell) . "</th>";
            }
            echo "</tr></thead><tbody>";
        } else {
            echo "<tr>";
            foreach ($row as $cell) {
                echo "<td>" . htmlspecialchars($cell) . "</td>";
            }
            echo "</tr>";
        }
        
        $rowCount++;
        if ($rowCount >= 11) break; // Solo mostrar las primeras 10 filas de datos + encabezado
    }
    
    echo "</tbody></table>";
    echo "<p><strong>Total de filas:</strong> " . count($rows) . " (incluyendo encabezado)</p>";
    
    // Mostrar encabezados detectados
    $headers = $rows[0] ?? [];
    echo "<hr>";
    echo "<h3>Encabezados detectados:</h3>";
    echo "<ul>";
    foreach ($headers as $index => $header) {
        echo "<li><strong>Columna $index:</strong> " . htmlspecialchars($header) . "</li>";
    }
    echo "</ul>";
    
    // Mostrar CSV raw (primeros 500 caracteres)
    echo "<hr>";
    echo "<h3>CSV Raw (primeros 500 caracteres):</h3>";
    echo "<pre style='background: #f4f4f4; padding: 15px; overflow-x: auto;'>";
    echo htmlspecialchars(substr($csvData, 0, 500));
    echo "...</pre>";
    
    echo "<hr>";
    echo "<h3 style='color: green;'>✓ Prueba exitosa!</h3>";
    echo "<p>El CSV se puede descargar y procesar correctamente.</p>";
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>✗ Error:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='../index.php'>← Volver al Dashboard</a></p>";
?>
