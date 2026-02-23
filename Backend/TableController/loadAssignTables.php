<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
include '../DBMSConector/db_connect.php';

// 1. Check if table_id is passed in the URL (e.g. ?table_id=5)
$table_number = isset($_GET['table_id']) ? $conn->real_escape_string($_GET['table_id']) : null;

if ($table_number) {
    // --- SCENARIO A: FILTERED BY TABLE ---
    // SQL: Find table -> Get Permissions -> Get Stalls
    $sql = "SELECT s.* FROM stalls s
            JOIN table_permissions tp ON s.id = tp.stall_id
            JOIN physical_tables pt ON tp.table_id = pt.id
            WHERE pt.table_number = '$table_number'
            ORDER BY s.id DESC";
            
} else {
    // --- SCENARIO B: SHOW ALL (No Table Scanned) ---
    $sql = "SELECT * FROM stalls ORDER BY id DESC";
}

$result = $conn->query($sql);
$stalls = array();

if ($result) {
    while($row = $result->fetch_assoc()) {
        $stalls[] = $row;
    }
}

echo json_encode($stalls);
$conn->close();
?>