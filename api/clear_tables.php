<?php
header('Content-Type: application/json');
require_once '../config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception("No se pudo conectar a la base de datos");
    }
    
    // Lista de tablas a limpiar
    $tables = ['campañas', 'ingles', 'pausas', 'puntos_adicionales', 'sync_log'];
    
    foreach ($tables as $table) {
        $query = "TRUNCATE TABLE " . pg_escape_identifier($conn, $table) . " RESTART IDENTITY CASCADE";
        $result = pg_query($conn, $query);
        
        if (!$result) {
            throw new Exception("Error al limpiar tabla {$table}: " . pg_last_error($conn));
        }
    }
    
    pg_close($conn);
    
    echo json_encode([
        'success' => true,
        'message' => 'Todas las tablas han sido limpiadas'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
