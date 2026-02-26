<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$table = $_GET['table'] ?? '';
$id = $_GET['id'] ?? '';

if (empty($table) || empty($id)) {
    echo json_encode(['success' => false, 'message' => 'Parámetros inválidos']);
    exit;
}

// Validar nombre de tabla
$validTables = ['campañas', 'ingles', 'pausas', 'puntos_adicionales'];
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
    
    $query = "DELETE FROM " . pg_escape_identifier($conn, $table) . " WHERE id = $1";
    $result = pg_query_params($conn, $query, [$id]);
    
    if (!$result) {
        throw new Exception(pg_last_error($conn));
    }
    
    pg_close($conn);
    
    echo json_encode([
        'success' => true,
        'message' => 'Registro eliminado exitosamente'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
