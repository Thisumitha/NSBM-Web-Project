<?php
// Backend/stallController/stall_login_check.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Disable error reporting display to prevent HTML errors breaking JSON
error_reporting(0); 
ini_set('display_errors', 0);

include '../DBMSConector/db_connect.php';

$response = array();

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Prepare SQL to prevent SQL Injection
    $stmt = $conn->prepare("SELECT id, name, password FROM stalls WHERE username = ?");
    if($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Check Password (Plain text comparison as per your setup)
            if ($password === $row['password']) {
                $response['status'] = 'success';
                $response['stall_id'] = $row['id'];
                $response['stall_name'] = $row['name'];
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Invalid Password';
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'User not found';
        }
        $stmt->close();
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Database query failed';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid Request Method';
}

echo json_encode($response);
$conn->close();
?>