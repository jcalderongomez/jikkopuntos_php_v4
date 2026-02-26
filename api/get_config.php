<?php
header('Content-Type: application/json');

$configFile = '../config/google_sheets_config.json';

if (!file_exists($configFile)) {
    echo json_encode([
        'success' => false,
        'message' => 'Configuración no encontrada'
    ]);
    exit;
}

try {
    $config = json_decode(file_get_contents($configFile), true);
    
    echo json_encode([
        'success' => true,
        'config' => $config
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
