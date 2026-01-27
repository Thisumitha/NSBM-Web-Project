<?php
// 1. Capture the data sent from the HTML form
$user_input = $_POST['username'];
$pass_input = $_POST['password'];

// 2. Define the "correct" credentials for testing
$correct_username = "NSBM-12345";
$correct_password = "password123";

// 3. Check if the input matches our records
if ($user_input === $correct_username && $pass_input === $correct_password) {
    
    // ✅ SUCCESS: Redirect to the Dashboard
    // Make sure your dashboard file is named 'dashboard.html' or 'index.html'
    header("Location: ../../frontend/pages/dashboard.html"); 
    exit(); // Always call exit() after a header redirect

} else {
    
    // ❌ FAILURE: Show an error
    echo "<h1>Login Failed</h1>";
    echo "<p>Invalid Student ID or Password. Please <a href='../../frontend/pages/login.html'>try again</a>.</p>";
}
?>