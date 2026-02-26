<?php
header('Content-Type: application/json');
require_once '../config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception("No se pudo conectar a la base de datos");
    }
    
    // Leer el archivo SQL
    $sqlFile = '../database.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("Archivo database.sql no encontrado");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Ejecutar el SQL
    $result = pg_query($conn, $sql);
    
    if (!$result) {
        throw new Exception(pg_last_error($conn));
    }
    
    pg_close($conn);
    
    echo json_encode([
        'success' => true,
        'message' => 'Base de datos reseteada exitosamente'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
