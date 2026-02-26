<?php
header('Content-Type: application/json');

// Obtener datos JSON del request
$input = json_decode(file_get_contents('php://input'), true);

$configFile = '../config/google_sheets_config.json';

// Crear archivo de configuración
$configData = [
    'csv_urls' => $input['csv_urls'] ?? [],
    'updated_at' => date('Y-m-d H:i:s')
];

try {
    $result = file_put_contents($configFile, json_encode($configData, JSON_PRETTY_PRINT));
    
    if ($result === false) {
        throw new Exception("No se pudo guardar el archivo de configuración");
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Configuración guardada exitosamente'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
