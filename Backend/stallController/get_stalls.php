<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
include '../DBMSConector/db_connect.php';

$sql = "SELECT * FROM stalls ORDER BY id DESC";
$result = $conn->query($sql);

$stalls = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $stalls[] = $row;
    }
}

echo json_encode($stalls);
$conn->close();
?>