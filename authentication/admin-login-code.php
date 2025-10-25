<?php 
// Database configuration file
include '../database/config.php';
// Start session
session_start();


if (isset($_POST['login_admin']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = 'admin';

    // Prepare and execute the SQL statement
    $sql = "SELECT * FROM users WHERE username = ? AND role = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $role);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $admin['password'])) {
            // Password is correct, set session variables
            $_SESSION['admin_authenticated'] = true;
            // $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['username'] = $admin['username'];
            $_SESSION['email'] = $admin['email'];
            $_SESSION['role'] = $admin['role'];
            $_SESSION['logged'] = "Welcome back, " . $admin['username'];
            $_SESSION['logged_icon'] = "success";

            // Redirect to dashboard or appropriate page
            header("Location: ../admin/homepage.php");
            exit();
        } else {
            // Invalid password
            echo "<script>alert('Invalid username or password. Please try again.'); window.location.href = 'user-login.php';</script>";
            exit();
        }
    } else {
        // User not found
        echo "<script>alert('Invalid username or password. Please try again.'); window.location.href = 'user-login.php';</script>";
        exit();
    }
}

?>