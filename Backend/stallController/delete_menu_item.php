<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include '../DBMSConector/db_connect.php';

$response = array();

$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($data['id'])) {
        $id = $data['id'];

        $stmt = $conn->prepare("DELETE FROM menu_items WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response['status'] = 'success';
                $response['message'] = "Item deleted successfully";
            } else {


                $response['status'] = 'success';
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