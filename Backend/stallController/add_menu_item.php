<?php
include '../DBMSConector/db_connect.php';

$stall_id = $_POST['stall_id'];
$name = $_POST['name'];
$price = $_POST['price'];
$category = $_POST['category'];

$sql = "INSERT INTO menu_items (stall_id, item_name, price, category) VALUES ('$stall_id', '$name', '$price', '$category')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["message" => "Item added successfully"]);
} else {
    echo json_encode(["message" => "Error adding item"]);
}
$conn->close();
?>