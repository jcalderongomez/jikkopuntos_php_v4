<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$table = $_GET['table'] ?? '';

if (empty($table)) {
    echo json_encode(['success' => false, 'message' => 'Tabla no especificada']);
    exit;
}

// Validar nombre de tabla
$validTables = ['campañas', 'participacion_campanas', 'ingles', 'pausas', 'puntos_adicionales', 'empleados', 'sync_log'];
if (!in_array($table, $validTables)) {
    echo json_encode(['success' => false, 'message' => 'Tabla inválida']);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception("No se pudo conectar a la base de datos");
    }
    
    // Escapar nombre de tabla (PostgreSQL permite UTF-8)
    $query = "SELECT * FROM " . pg_escape_identifier($conn, $table) . " ORDER BY id ASC";
    $result = pg_query($conn, $query);
    
    if (!$result) {
        throw new Exception(pg_last_error($conn));
    }
    
    $data = [];
    while ($row = pg_fetch_assoc($result)) {
        $data[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $data,
        'count' => count($data)
    ]);
    
    pg_close($conn);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
