<?php
$servername = "localhost";
$username = "root";      // Default XAMPP username
$password = "Thisu@2006";          // Default XAMPP password is empty
$dbname = "nsbm_canteen_db";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>