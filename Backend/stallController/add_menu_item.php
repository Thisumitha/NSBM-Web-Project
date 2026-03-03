<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../DBMSConector/db_connect.php';

$response = array();


$table_sql = "CREATE TABLE IF NOT EXISTS menu_items (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    stall_id INT(11) NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    category VARCHAR(100) NOT NULL,
    image_url VARCHAR(255) DEFAULT NULL,
    is_available TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    -- Note: You can add FOREIGN KEY (stall_id) REFERENCES stalls(id) here if you want strict linking
)";

if (!$conn->query($table_sql)) {
    echo json_encode(["status" => "error", "message" => "Table Creation Error: " . $conn->error]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stall_id = $_POST['stall_id'] ?? 0;
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? 0.00;
    $category = $_POST['category'] ?? 'Main';

    $final_image_path = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100%25' height='100%25' viewBox='0 0 1 1'%3E%3Crect fill='%23cccccc' width='1' height='1'/%3E%3C/svg%3E";


    $upload_dir = "../uploads/";

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $new_filename = "menu_" . time() . "_" . uniqid() . "." . $file_extension;
        $target_file = $upload_dir . $new_filename;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {


            $final_image_path = "uploads/" . $new_filename;
        }
    }


    $stmt = $conn->prepare("INSERT INTO menu_items (stall_id, item_name, price, category, image_url) VALUES (?, ?, ?, ?, ?)");

    if ($stmt) {

        $stmt->bind_param("isdss", $stall_id, $name, $price, $category, $final_image_path);

        if ($stmt->execute()) {
            echo json_encode([
                "status" => "success",
                "message" => "Item added successfully",
                "image_url" => $final_image_path
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Database Insert Error: " . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Statement Error: " . $conn->error]);
    }

} else {
    echo json_encode(["status" => "error", "message" => "Invalid Request Method"]);
}

$conn->close();
?>