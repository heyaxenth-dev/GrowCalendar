<?php
/**
 * Check crop schedules
 */

include 'config.php';

echo "Checking crop schedules...\n";

try {
    $result = $conn->query("SELECT id, user_id, crop_id, status, planting_date FROM crop_schedules ORDER BY id");
    
    if ($result->num_rows > 0) {
        echo "Crop schedules:\n";
        while ($row = $result->fetch_assoc()) {
            echo "ID: " . $row['id'] . " - User: " . $row['user_id'] . " - Crop: " . $row['crop_id'] . " - Status: " . $row['status'] . " - Planted: " . $row['planting_date'] . "\n";
        }
    } else {
        echo "No crop schedules found.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$conn->close();
?>
