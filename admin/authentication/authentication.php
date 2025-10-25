<?php
session_start();

// Check if the admin is not authenticated
if (!isset($_SESSION['admin_authenticated'])) {
    // Set session variables for status message
    $_SESSION['status'] = "Denied Access!";
    $_SESSION['status_text'] = "Please Login to Access the Page";
    $_SESSION['status_code'] = "warning";
    $_SESSION['status_btn'] = "Back";
    
    // Redirect to login page
    header("Location: /GrowCalendar/index");
    exit; // Exit script to prevent further execution
}

?>