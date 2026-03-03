<?php
header('Content-Type: application/json');
include '../DBMSConector/db_connect.php';

if(isset($_GET['stall_id'])) {
    $stall_id = $_GET['stall_id'];

    $stall_id = $conn->real_escape_string($stall_id);

    $sql = "SELECT * FROM menu_items WHERE stall_id='$stall_id' ORDER BY id DESC";
    $result = $conn->query($sql);

    $rows = array();
    while($r = $result->fetch_assoc()) {
        $rows[] = $r;
    }
    echo json_encode($rows);
} else {
    echo json_encode([]);
}

$conn->close();
?>