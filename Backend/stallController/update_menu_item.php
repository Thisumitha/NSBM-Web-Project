<?php
// update_menu_item.php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
include '../DBMSConector/db_connect.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'] ?? null;
    $stall_id = $_POST['stall_id'] ?? 0;
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? 0.00;
    $category = $_POST['category'] ?? 'Main';

    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'Item ID is required']);
        exit;
    }

    // Handle Image Upload
    $upload_dir = "../uploads/";
    $final_image_path = null;

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

    // Update Query
    if ($final_image_path) {
        $sql = "UPDATE menu_items SET item_name=?, price=?, category=?, image_url=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdssi", $name, $price, $category, $final_image_path, $id);
    } else {
        $sql = "UPDATE menu_items SET item_name=?, price=?, category=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdsi", $name, $price, $category, $id);
    }

    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = "Item updated successfully";
    } else {
        $response['status'] = 'error';
        $response['message'] = "Database Update Error: " . $stmt->error;
    }
    $stmt->close();

} else {
    $response['status'] = 'error';
    $response['message'] = "Invalid Request Method";
}

echo json_encode($response);
$conn->close();
?>