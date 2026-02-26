<?php
require_once __DIR__ . '/../config/database.php';

$db = new Database();
$conn = $db->getConnection();

// Verificar columnas actuales
$result = pg_query($conn, "SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'pausas' ORDER BY ordinal_position");

echo "Columnas actuales de la tabla pausas:\n\n";
while ($row = pg_fetch_assoc($result)) {
    echo "  - {$row['column_name']} ({$row['data_type']})\n";
}

echo "\n";

// Agregar columna observaciones si no existe
$addColumn = pg_query($conn, "ALTER TABLE pausas ADD COLUMN IF NOT EXISTS observaciones TEXT");
if ($addColumn) {
    echo "✓ Columna 'observaciones' agregada o ya existe\n";
} else {
    echo "✗ Error al agregar columna: " . pg_last_error($conn) . "\n";
}

pg_close($conn);
?>
