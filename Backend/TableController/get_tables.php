<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../DBMSConector/db_connect.php';

$sql = "SELECT t.id, t.table_number, 
        GROUP_CONCAT(s.name SEPARATOR ', ') as allowed_stalls
        FROM physical_tables t
        LEFT JOIN table_permissions tp ON t.id = tp.table_id
        LEFT JOIN stalls s ON tp.stall_id = s.id
        GROUP BY t.id
        ORDER BY t.id DESC";

$result = $conn->query($sql);
$tables = [];

while($row = $result->fetch_assoc()) {
    $tables[] = $row;
}

echo json_encode($tables);
$conn->close();
?>