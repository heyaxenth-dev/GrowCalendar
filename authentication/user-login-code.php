<?php 
// Database configuration file
include '../database/config.php';
// Start session
session_start();


if (isset($_POST['login_user']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute the SQL statement
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Password is correct, set session variables
            $_SESSION['user_authenticated'] = true;
            // $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            // $_SESSION['role'] = $user['role'];

            // Redirect to dashboard or appropriate page
            header("Location: ../client/homepage.php");
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