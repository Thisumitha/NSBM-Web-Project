<?php


header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
include '../DBMSConector/db_connect.php';

$response = array();

$placeholder = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100%25' height='100%25' viewBox='0 0 1 1'%3E%3Crect fill='%23cccccc' width='1' height='1'/%3E%3C/svg%3E";

$sql_menu = "UPDATE menu_items SET image_url = ? WHERE image_url LIKE '%via.placeholder.com%' OR image_url = 'https://via.placeholder.com/150'";
$stmt_menu = $conn->prepare($sql_menu);

if ($stmt_menu) {
    $stmt_menu->bind_param("s", $placeholder);
    $stmt_menu->execute();
    $response['menu_items_updated'] = $stmt_menu->affected_rows;
    $stmt_menu->close();
} else {
    $response['menu_error'] = $conn->error;
}

$sql_stall = "UPDATE stalls SET image_path = ? WHERE image_path LIKE '%via.placeholder.com%' OR image_path = 'https://via.placeholder.com/100'";
$stmt_stall = $conn->prepare($sql_stall);

if ($stmt_stall) {
    $stmt_stall->bind_param("s", $placeholder);
    $stmt_stall->execute();
    $response['stalls_updated'] = $stmt_stall->affected_rows;
    $stmt_stall->close();
} else {
    $response['stall_error'] = $conn->error;
}

echo json_encode($response);
$conn->close();
?>