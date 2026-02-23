<?php
// update_stall.php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
include '../DBMSConector/db_connect.php';

$response = array();

// Helper to update stall details
function updateStall($conn, $id, $name, $category, $owner, $status, $username, $password, $image_path = null)
{
    if ($image_path) {
        $sql = "UPDATE stalls SET name=?, category=?, owner=?, status=?, username=?, password=?, image_path=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssssssi", $name, $category, $owner, $status, $username, $password, $image_path, $id);
            return $stmt;
        }
    } else {
        $sql = "UPDATE stalls SET name=?, category=?, owner=?, status=?, username=?, password=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssssssi", $name, $category, $owner, $status, $username, $password, $id);
            return $stmt;
        }
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Collect Data
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'] ?? '';
    $category = $_POST['category'] ?? '';
    $owner = $_POST['owner'] ?? '';
    $status = $_POST['status'] ?? 'Open';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Stall ID is required']);
        exit;
    }

    // 2. Handle Image Upload
    $upload_dir = "../uploads/";
    $image_db_path = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        if (!is_dir($upload_dir))
            mkdir($upload_dir, 0777, true);

        $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $file_name = time() . "_" . uniqid() . "." . $file_extension;
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_db_path = "uploads/" . $file_name;
        }
    }

    // 3. Update Database
    $stmt = updateStall($conn, $id, $name, $category, $owner, $status, $username, $password, $image_db_path);

    if ($stmt && $stmt->execute()) {
        $response['success'] = true;
        $response['message'] = "Stall updated successfully";
        if ($image_db_path)
            $response['new_image_path'] = $image_db_path;
        $stmt->close();
    } else {
        $response['success'] = false;
        $response['message'] = "Database Update Error: " . ($stmt ? $stmt->error : $conn->error);
    }

} else {
    $response['success'] = false;
    $response['message'] = "Invalid Request Method";
}

echo json_encode($response);
$conn->close();
?>