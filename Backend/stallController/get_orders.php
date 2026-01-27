<?php
include '../DBMSConector/db_connect.php';
$stall_id = $_GET['stall_id'];

$sql = "SELECT * FROM orders WHERE stall_id='$stall_id' ORDER BY created_at DESC";
$result = $conn->query($sql);

$rows = array();
while($r = $result->fetch_assoc()) {
    $rows[] = $r;
}
echo json_encode($rows);
$conn->close();
?>