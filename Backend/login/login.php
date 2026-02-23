<?php

$user_input = $_POST['username'];
$pass_input = $_POST['password'];

$correct_username = "NSBM-12345";
$correct_password = "password123";


if ($user_input === $correct_username && $pass_input === $correct_password) {

    header("Location: ../../frontend/pages/dashboard.html"); 
    exit(); 

} else {
    
    echo "<h1>Login Failed</h1>";
    echo "<p>Invalid Student ID or Password. Please <a href='../../frontend/pages/login.html'>try again</a>.</p>";
}
?>