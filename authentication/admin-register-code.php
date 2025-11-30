<?php
session_start();
// Database configuration file 
include '../database/config.php';


if (isset($_POST['create_admin_acc']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    //default role assignment
    $role = 'admin';

    // Check if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['status'] = "Error!";
        $_SESSION['status_text'] = "Passwords do not match.";
        $_SESSION['status_code'] = "error";
        $_SESSION['status_btn'] = "Back";
        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit();
    }

    // Check if email already exists in the database
    $check_email_sql = "SELECT email FROM users WHERE email = ?";
    $check_email_stmt = $conn->prepare($check_email_sql);
    $check_email_stmt->bind_param("s", $email);
    $check_email_stmt->execute();
    $check_email_result = $check_email_stmt->get_result();

    if ($check_email_result->num_rows > 0) {
        $_SESSION['status'] = "Error!";
        $_SESSION['status_text'] = "This email is already registered. Please use a different email or try logging in.";
        $_SESSION['status_code'] = "error";
        $_SESSION['status_btn'] = "Back";
        $check_email_stmt->close();
        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit();
    }
    $check_email_stmt->close();

    // Check if username already exists in the database
    $check_username_sql = "SELECT username FROM users WHERE username = ?";
    $check_username_stmt = $conn->prepare($check_username_sql);
    $check_username_stmt->bind_param("s", $username);
    $check_username_stmt->execute();
    $check_username_result = $check_username_stmt->get_result();

    if ($check_username_result->num_rows > 0) {
        $_SESSION['status'] = "Error!";
        $_SESSION['status_text'] = "This username is already taken. Please choose a different username.";
        $_SESSION['status_code'] = "error";
        $_SESSION['status_btn'] = "Back";
        $check_username_stmt->close();
        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit();
    }
    $check_username_stmt->close();

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (role, firstname, lastname, email, username, password) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $role, $firstname, $lastname, $email, $username, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['status'] = "Success!";
        $_SESSION['status_text'] = "Account created successfully! You can now log in.";
        $_SESSION['status_code'] = "success";
        $_SESSION['status_btn'] = "Login";

        header("Location: admin-login.php");
        // echo "<script>alert('Account created successfully! You can now log in.'); window.location.href = 'admin-login.php';</script>";
    } else {
        $_SESSION['status'] = "Error!";
        $_SESSION['status_text'] = "Error creating account. Please try again.";
        $_SESSION['status_code'] = "error";
        $_SESSION['status_btn'] = "Back";
        
        header("Location: admin-register.php");
        
        // echo "<script>alert('Error creating account: " . $stmt->error . "'); window.location.href = 'admin-register.php';</script>";
    }

    $stmt->close();
    $conn->close();
}

?>