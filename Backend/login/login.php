<?php
// 1. Capture the data sent from the HTML form
// $_POST is a special PHP variable that holds the form data
$user_input = $_POST['username'];
$pass_input = $_POST['password'];

// 2. Define the "correct" credentials for testing
$correct_username = "NSBM-12345";
$correct_password = "password123";

// 3. Check if the input matches our records
if ($user_input === $correct_username && $pass_input === $correct_password) {
    // Success: Redirect to a welcome page or show a message
    echo "<h1>Welcome to NSBM Edge!</h1>";
    echo "<p>Login successful. Redirecting to your dashboard...</p>";
} else {
    // Failure: Show an error
    echo "<h1>Login Failed</h1>";
    echo "<p>Invalid Student ID or Password. Please <a href='index.html'>try again</a>.</p>";
}
?>