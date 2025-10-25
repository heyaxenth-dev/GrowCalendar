<?php
session_start();
// Database configuration file 
include '../database/config.php';


if (isset($_POST['create_user_acc']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    //default role assignment
    $role = 'user';

     // Check if username or email already exists
    if ($password !== $confirm_password) {
        $_SESSION['status'] = "Error!";
        $_SESSION['status_text'] = "Passwords do not match.";
        $_SESSION['status_code'] = "error";
        $_SESSION['status_btn'] = "Back";
        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (role, firstname, lastname, email, username, password) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $role, $firstname, $lastname, $email, $username, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['status'] = "Success!";
        $_SESSION['status_text'] = "Accoount created successfully! You can now log in.";
        $_SESSION['status_code'] = "success";
        $_SESSION['status_btn'] = "Login";
        header("Location: ../user-login.php");

        // echo "<script>alert('Account created successfully! You can now log in.'); window.location.href = 'user-login.php';</script>";
    } else {
        $_SESSION['status'] = "Error!";
        $_SESSION['status_text'] = "Error creating account. Please try again.";
        $_SESSION['status_code'] = "error";
        $_SESSION['status_btn'] = "Back";
        echo "<script>alert('Error creating account: " . $stmt->error . "'); window.location.href = '../user-register.php';</script>";
    }

    $stmt->close();
    $conn->close();
}

?>