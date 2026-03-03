<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

include '../DBMSConector/db_connect.php';

$response = array();

$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($data['id'])) {
        $id = $data['id'];

        $stmt = $conn->prepare("DELETE FROM stalls WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response['success'] = true;
                $response['message'] = "Stall deleted successfully";
            } else {
                $response['success'] = false;
                $response['message'] = "Stall not found or already deleted";
            }
        } else {
            $response['success'] = false;
            $response['message'] = "Database Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $response['success'] = false;
        $response['message'] = "Stall ID is required";
    }

} else {
    $response['success'] = false;
    $response['message'] = "Invalid Request Method. Use POST.";
}

echo json_encode($response);
$conn->close();
?>
