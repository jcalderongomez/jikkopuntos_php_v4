<?php
require_once 'config/database.php';

$db = new Database();
$conn = $db->getConnection();

$cedula = '1083047212';
$fechaInicio = '2026-01-01';
$fechaFin = '2026-01-31';

$query = "SELECT tipo_pausa, fecha FROM pausas 
          WHERE empleado_id = $1 
          AND fecha >= $2 
          AND fecha <= $3 
          ORDER BY fecha";

$result = pg_query_params($conn, $query, array($cedula, $fechaInicio, $fechaFin));

echo "=== PAUSAS ENERO 2026 - CEDULA 1083047212 ===\n\n";
echo str_pad('Tipo', 30) . str_pad('Fecha', 15) . "Puntos\n";
echo str_repeat('-', 50) . "\n";

$totalPuntos = 0;

while ($row = pg_fetch_assoc($result)) {
    $tipoPausa = $row['tipo_pausa'];
    $puntos = 15; // Por defecto
    
    if (stripos($tipoPausa, 'jueves de pausar con todos') !== false) {
        $puntos = 50;
    } else if ($tipoPausa === '1') {
        $puntos = 15;
    } else if ($tipoPausa === '2') {
        $puntos = 30;
    } else if ($tipoPausa === '3') {
        $puntos = 45;
    }
    
    $totalPuntos += $puntos;
    echo str_pad($tipoPausa, 30) . str_pad($row['fecha'], 15) . $puntos . "\n";
}

echo str_repeat('-', 50) . "\n";
echo "TOTAL PUNTOS ENERO 2026: $totalPuntos\n";
echo "Total de registros: " . pg_num_rows($result) . "\n";

pg_close($conn);
