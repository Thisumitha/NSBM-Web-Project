<?php
include '../DBMSConector/db_connect.php'; // Reuse your existing connection file

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM stalls WHERE username='$username' AND password='$password'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(["status" => "success", "stall_id" => $row['id'], "stall_name" => $row['name']]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
}
$conn->close();
?>