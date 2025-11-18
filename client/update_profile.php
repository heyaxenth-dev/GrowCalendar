<?php
session_start();
include '../database/config.php';

if (isset($_POST['save_profile'])) {

    $firstname = trim($_POST['firstname']);
    $lastname  = trim($_POST['lastname']);
    $username  = trim($_POST['username']);
    $email     = trim($_POST['email']);
    $user_id   = $_POST['user_id'];

     // Check if username OR email already exists (other accounts)
    $check = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
    $check->bind_param("ssi", $username, $email, $user_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $_SESSION['error'] = "Username or Email already taken!";
        header("Location: profile.php");
        exit();
    }

    // Update profile
    $stmt = $conn->prepare("UPDATE users SET firstname=?, lastname=?, username=?, email=? WHERE id=?");
    $stmt->bind_param("ssssi", $firstname, $lastname, $username, $email, $user_id);

    if ($stmt->execute()) {
        $_SESSION['status'] = "Success!";
        $_SESSION['status_text'] = "Profile updated successfully.";
        $_SESSION['status_code'] = "success";
        $_SESSION['status_btn'] = "Okay";
    } else {
        $_SESSION['status'] = "Something went wrong!";
        $_SESSION['status_text'] = "Failed to update profile. Please try again.";
        $_SESSION['status_code'] = "error";
        $_SESSION['status_btn'] = "Retry";
    }

    header("Location: users-profile.php");
    exit();
}