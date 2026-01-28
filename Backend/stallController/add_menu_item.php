<?php
header('Content-Type: application/json');
include '../DBMSConector/db_connect.php';

$stall_id = $_POST['stall_id'];
$name = $_POST['name'];
$price = $_POST['price'];
$category = $_POST['category'];

// Default image if none uploaded
$image_url = "https://via.placeholder.com/150";

// Handle Image Upload
if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $target_dir = "../../uploads/"; // Make sure this folder exists
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
    $new_filename = uniqid() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // Save the path to database
        $image_db_path = "Backend/uploads/" . $new_filename; 
    }
}

$sql = "INSERT INTO menu_items (stall_id, item_name, price, category, image_url) 
        VALUES ('$stall_id', '$name', '$price', '$category', '$image_url')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["status" => "success", "message" => "Item added successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
}

$conn->close();
?>