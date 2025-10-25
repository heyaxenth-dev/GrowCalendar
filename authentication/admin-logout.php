<?php 
// Start session
session_start();
// Logout admin by destroying the session
// Check if admin is authenticated as an admin
if (isset($_SESSION['admin_authenticated'])) {
    // Set session variables for status message
    $_SESSION['status'] = "Logged Out Successfully!";
    $_SESSION['status_text'] = "You have been logged out.";
    $_SESSION['status_code'] = "success";
    $_SESSION['status_btn'] = "Done";

    // Unset session variables
    unset($_SESSION['admin_authenticated']);
    unset($_SESSION['username']);
    unset($_SESSION['email']);
    unset($_SESSION['role']);

    // Redirect to index page
    header("Location: ../index");
    exit; // Exit script to prevent further execution
}
?>