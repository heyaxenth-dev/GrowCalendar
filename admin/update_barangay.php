<?php
include '../database/config.php';

if (isset($_POST['user_id'], $_POST['barangay'])) {
    $user_id = $_POST['user_id'];
    $barangay = trim($_POST['barangay']);

    $stmt = $conn->prepare("UPDATE users SET barangay = ? WHERE id = ?");
    $stmt->bind_param("si", $barangay, $user_id);

    if ($stmt->execute()) {
        echo "Barangay successfully updated.";
    } else {
        echo "Error updating barangay.";
    }
} else {
    echo "Invalid request.";
}
?>