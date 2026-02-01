<?php
header('Content-Type: application/json');
include '../DBMSConector/db_connect.php';

$table_id = $_GET['table_id'] ?? '';

if (!$table_id) {
    echo json_encode(["status" => "error", "message" => "No table ID provided"]);
    exit;
}

// 1. Check if table exists and has rules
$stmt = $conn->prepare("SELECT * FROM physical_tables WHERE table_number = ?");
$stmt->bind_param("s", $table_id);
$stmt->execute();
$result = $stmt->get_result();
$table = $result->fetch_assoc();

if (!$table) {
    // If table isn't in DB, assume it's a generic/common table for now
    echo json_encode(["status" => "success", "type" => "common"]);
} else {
    if ($table['exclusive_stall_id'] != NULL) {
        // EXCLUSIVE TABLE: Return the ID of the allowed stall
        echo json_encode([
            "status" => "success", 
            "type" => "exclusive", 
            "allowed_stall_id" => $table['exclusive_stall_id']
        ]);
    } else {
        // COMMON TABLE
        echo json_encode(["status" => "success", "type" => "common"]);
    }
}

$conn->close();
?>