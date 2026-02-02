<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../DBMSConector/db_connect.php';

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->order_id) && !empty($data->status) && !empty($data->stall_id)) {
    
    $order_id = $conn->real_escape_string($data->order_id);
    $status   = $conn->real_escape_string($data->status);
    $stall_id = $conn->real_escape_string($data->stall_id);

    // Update ALL items for this order belonging to this stall
    $sql = "UPDATE order_items 
            SET status = '$status' 
            WHERE order_id = '$order_id' AND stall_id = '$stall_id'";

    if($conn->query($sql)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => $conn->error]);
    }
}
$conn->close();
?>