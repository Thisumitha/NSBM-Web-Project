<?php
// add_stall.php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
include '../DBMSConector/db_connect.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Collect Text Data
    $name = $_POST['name'] ?? '';
    $category = $_POST['category'] ?? '';
    $owner = $_POST['owner'] ?? '';
    $status = $_POST['status'] ?? 'Open';
    
    // 2. Handle Image Upload
    // NOTE: This path is relative to the PHP file. 
    // If php is in "Backend/stallController", this goes to "Backend/uploads"
    $upload_dir = "../uploads/"; 
    $image_db_path = "https://via.placeholder.com/100"; // Default

    // --- FIX 1: Create directory if it doesn't exist ---
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            $response['success'] = false;
            $response['message'] = "Failed to create 'uploads' directory. Check permissions.";
            echo json_encode($response);
            exit;
        }
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $file_name = time() . "_" . uniqid() . "." . $file_extension; // Unique name
        $target_file = $upload_dir . $file_name;
        
        // --- FIX 2: Check for Move Success ---
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Save path compatible with HTML (frontend needs to step back to find it)
            $image_db_path = "uploads/" . $file_name; 
        } else {
            // If move fails, we note it but still save the text data
            $response['upload_error'] = "Could not move file. Check folder permissions.";
        }
    }

    // 3. Insert into Database
    $stmt = $conn->prepare("INSERT INTO stalls (name, category, owner, status, image_path) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $category, $owner, $status, $image_db_path);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = "Stall created successfully";
        $response['image_path'] = $image_db_path; 
    } else {
        $response['success'] = false;
        $response['message'] = "Database Error: " . $stmt->error;
    }
    
    $stmt->close();
} else {
    $response['success'] = false;
    $response['message'] = "Invalid Request Method";
}

echo json_encode($response);
$conn->close();
?>