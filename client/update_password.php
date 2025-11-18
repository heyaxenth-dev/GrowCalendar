<?php
session_start();
include '../database/config.php';

if (isset($_POST['change_password'])) {

    $current_pw = $_POST['current_pw'];
    $new_pw     = $_POST['new_pw'];
    $confirm_pw = $_POST['confirm_pw'];
    $user_id    = $_POST['user_id'];

    if ($new_pw !== $confirm_pw) {
        $_SESSION['error'] = "New passwords do not match!";
        header("Location: profile.php");
        exit();
    }

    // Get current password hash
    $stmt = $conn->prepare("SELECT password FROM users WHERE id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($hash);
    $stmt->fetch();
    $stmt->close();

    // Verify current password
    if (!password_verify($current_pw, $hash)) {
        $_SESSION['error'] = "Current password is incorrect!";
        header("Location: users-profile.php");
        exit();
    }

    // Update password with secure hashing
    $new_hash = password_hash($new_pw, PASSWORD_DEFAULT);

    $update = $conn->prepare("UPDATE users SET password=? WHERE id=?");
    $update->bind_param("si", $new_hash, $user_id);

    if ($update->execute()) {
        $_SESSION['status'] = "Success!";
        $_SESSION['status_text'] = "Password updated successfully.";
        $_SESSION['status_code'] = "success";
        $_SESSION['status_btn'] = "Okay";
    } else {
        $_SESSION['status'] = "Something went wrong!";
        $_SESSION['status_text'] = "Failed to update password. Please try again.";
        $_SESSION['status_code'] = "error";
        $_SESSION['status_btn'] = "Retry";
    }

    header("Location: users-profile.php");
    exit();
}