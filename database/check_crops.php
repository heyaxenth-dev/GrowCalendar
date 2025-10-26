<?php
/**
 * Check existing crops in database
 */

include 'config.php';

echo "Checking existing crops...\n";

try {
    $result = $conn->query("SELECT id, name FROM crops ORDER BY id");
    
    if ($result->num_rows > 0) {
        echo "Available crops:\n";
        while ($row = $result->fetch_assoc()) {
            echo "ID: " . $row['id'] . " - " . $row['name'] . "\n";
        }
    } else {
        echo "No crops found in database.\n";
    }
    
    // Check users
    $result = $conn->query("SELECT id, username FROM users ORDER BY id");
    
    if ($result->num_rows > 0) {
        echo "\nAvailable users:\n";
        while ($row = $result->fetch_assoc()) {
            echo "ID: " . $row['id'] . " - " . $row['username'] . "\n";
        }
    } else {
        echo "\nNo users found in database.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$conn->close();
?>
