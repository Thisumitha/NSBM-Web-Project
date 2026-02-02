<?php
// add_stall.php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
include '../DBMSConector/db_connect.php';

$response = array();

// --- STEP 0: Create Table (Updated with username/password) ---
$table_sql = "CREATE TABLE IF NOT EXISTS stalls (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(255) NOT NULL,
    owner VARCHAR(255) NOT NULL,
    status VARCHAR(50) DEFAULT 'Open',
    username VARCHAR(100) NOT NULL, 
    password VARCHAR(255) NOT NULL,
    image_path VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($table_sql)) {
    $response['success'] = false;
    $response['message'] = "Table Creation Error: " . $conn->error;
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Collect Text Data (Added Username and Password)
    $name = $_POST['name'] ?? '';
    $category = $_POST['category'] ?? '';
    $owner = $_POST['owner'] ?? '';
    $status = $_POST['status'] ?? 'Open';
    $username = $_POST['username'] ?? ''; // New Field
    $password = $_POST['password'] ?? ''; // New Field
    
    // 2. Handle Image Upload
    $upload_dir = "../uploads/"; 
    $image_db_path = "https://via.placeholder.com/100"; // Default

    // Create directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            $response['success'] = false;
            $response['message'] = "Failed to create 'uploads' directory.";
            echo json_encode($response);
            exit;
        }
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $file_name = time() . "_" . uniqid() . "." . $file_extension;
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_db_path = "uploads/" . $file_name; 
        } else {
            $response['upload_error'] = "Could not move file.";
        }
    }

    // 3. Insert into Database (Updated to include username/password)
    // Note: In a real production app, you should hash the password (e.g., password_hash($password, PASSWORD_DEFAULT))
    // For now, we are storing it as plain text as per your example data.
    
    $stmt = $conn->prepare("INSERT INTO stalls (name, category, owner, status, username, password, image_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt) {
        // "sssssss" = 7 strings
        $stmt->bind_param("sssssss", $name, $category, $owner, $status, $username, $password, $image_db_path);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Stall created successfully";
            $response['image_path'] = $image_db_path; 
        } else {
            $response['success'] = false;
            $response['message'] = "Database Insert Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $response['success'] = false;
        $response['message'] = "Statement Preparation Error: " . $conn->error;
    }
    
} else {
    $response['success'] = false;
    $response['message'] = "Invalid Request Method";
}

echo json_encode($response);
$conn->close();
?>