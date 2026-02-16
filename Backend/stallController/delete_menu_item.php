<?php
// delete_menu_item.php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include '../DBMSConector/db_connect.php';

$response = array();

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($data['id'])) {
        $id = $data['id'];

        // Prepare delete statement
        $stmt = $conn->prepare("DELETE FROM menu_items WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response['status'] = 'success';
                $response['message'] = "Item deleted successfully";
            } else {
                // If 0 rows affected, it might be already deleted, but operation is successful in terms of 'item is gone'
                // Or maybe invalid ID. 
                $response['status'] = 'success'; // Treat as success to clear UI
                $response['message'] = "Item deleted or not found";
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = "Database Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $response['status'] = 'error';
        $response['message'] = "Item ID is required";
    }

} else {
    $response['status'] = 'error';
    $response['message'] = "Invalid Request Method";
}

echo json_encode($response);
$conn->close();
?>