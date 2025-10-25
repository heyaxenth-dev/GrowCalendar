<?php 
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

     // Check if username or email already exists
    if ($password !== $confirm_password) {
        $_SESSION['status'] = "Error!";
        $_SESSION['status_text'] = "Passwords do not match.";
        $_SESSION['status_code'] = "error";
        $_SESSION['status_btn'] = "Back";
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (role, firstname, lastname, email, username, password) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $role, $firstname, $lastname, $email, $username, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>alert('Account created successfully! You can now log in.'); window.location.href = 'admin-login.php';</script>";
    } else {
        echo "<script>alert('Error creating account: " . $stmt->error . "'); window.location.href = 'admin-register.php';</script>";
    }

    $stmt->close();
    $conn->close();
}

?>