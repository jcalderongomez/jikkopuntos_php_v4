<?php
header('Content-Type: application/json');
require_once '../config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        echo json_encode([
            'success' => false,
            'message' => 'No se pudo conectar a PostgreSQL. Verifique que PostgreSQL esté ejecutándose y las credenciales sean correctas.'
        ]);
        exit;
    }
    
    // Probar una consulta simple
    $result = pg_query($conn, "SELECT version()");
    
    if (!$result) {
        throw new Exception(pg_last_error($conn));
    }
    
    $version = pg_fetch_row($result);
    
    pg_close($conn);
    
    echo json_encode([
        'success' => true,
        'message' => 'Conexión exitosa a PostgreSQL',
        'version' => $version[0]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
