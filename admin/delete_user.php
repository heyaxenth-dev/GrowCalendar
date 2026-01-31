<?php
include 'authentication/authentication.php';
include '../database/config.php';

if (!isset($_POST['user_id'])) {
    echo "Invalid request.";
    exit;
}

$user_id = (int) $_POST['user_id'];
if ($user_id <= 0) {
    echo "Invalid user.";
    exit;
}

// Only allow deleting users with role 'user' (technologists), not admins
$check = $conn->prepare("SELECT id, role FROM users WHERE id = ?");
$check->bind_param("i", $user_id);
$check->execute();
$res = $check->get_result();
$user = $res->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit;
}

if ($user['role'] !== 'user') {
    echo "Cannot delete this user.";
    exit;
}

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);

echo $stmt->execute() ? "User successfully deleted." : "Error deleting user.";
?>
