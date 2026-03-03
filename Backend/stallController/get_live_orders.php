<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../DBMSConector/db_connect.php';

$stall_id = $_GET['stall_id'] ?? 0;

if($stall_id) {


    $sql = "SELECT 
                o.id as order_id,
                o.table_id,
                DATE_FORMAT(o.created_at, '%h:%i %p') as order_time,
                oi.item_name,
                oi.quantity,
                oi.price,
                oi.status as item_status
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            WHERE oi.stall_id = '$stall_id' 
            AND oi.status != 'completed'
            ORDER BY o.created_at ASC";

    $result = $conn->query($sql);
    $rows = [];
    while($r = $result->fetch_assoc()) {
        $rows[] = $r;
    }
    echo json_encode($rows);
} else {
    echo json_encode([]);
}
$conn->close();
?>