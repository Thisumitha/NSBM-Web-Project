<?php
header('Content-Type: application/json');
include '../DBMSConector/db_connect.php';

// Get JSON data sent from the dashboard
$data = json_decode(file_get_contents("php://input"), true);

if(isset($data['name'])) {
    $name = $data['name'];
    $category = $data['category'];
    $owner = $data['owner'];
    $status = $data['status'];

    $sql = "INSERT INTO stalls (name, category, owner, status) VALUES ('$name', '$category', '$owner', '$status')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "New stall created successfully", "id" => $conn->insert_id]);
    } else {
        echo json_encode(["message" => "Error: " . $sql . "<br>" . $conn->error]);
    }
}
$conn->close();
?>