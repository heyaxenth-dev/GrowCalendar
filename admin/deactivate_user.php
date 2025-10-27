<?php
include '../database/config.php';

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $stmt = $conn->prepare("UPDATE users SET status = 'Inactive' WHERE id = ?");
    $stmt->bind_param("i", $user_id);

    echo $stmt->execute() ? "User successfully deactivated." : "Error deactivating user.";
} else {
    echo "Invalid request.";
}
?>