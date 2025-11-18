<?php 
// Database configuration file
include '../database/config.php';
// Start session
session_start();


if (isset($_POST['login_user']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = 'user';

    // Prepare and execute the SQL statement
    $sql = "SELECT * FROM users WHERE username = ? AND role = '$role'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Password is correct, set session variables and update last login date
            $update_sql = "UPDATE users SET last_login = NOW() WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $user['id']);
            $update_stmt->execute();

            $_SESSION['user_authenticated'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['logged'] = "Welcome back, " . $user['username'];
            $_SESSION['logged_icon'] = "success";

            // Redirect to dashboard or appropriate page
            header("Location: ../client/homepage.php");
            exit();
        } else {
            // Invalid password
            $_SESSION['status'] = "Login Failed!";
            $_SESSION['status_text'] = "Invalid username or password. Please try again.";
            $_SESSION['status_code'] = "error";
            $_SESSION['status_btn'] = "Retry";

            header("Location: ./user-login.php");
            // echo "<script>alert('Invalid username or password. Please try again.'); window.location.href = 'user-login.php';</script>";
            exit();
        }
    } else {
        // User not found
        $_SESSION['status'] = "Login Failed!";
        $_SESSION['status_text'] = "Invalid username or password. Please try again.";
        $_SESSION['status_code'] = "error";
        $_SESSION['status_btn'] = "Retry";
        header("Location: ./user-login.php");
        // echo "<script>alert('Invalid username or password. Please try again.'); window.location.href = 'user-login.php';</script>";
        exit();
    }
}

?>