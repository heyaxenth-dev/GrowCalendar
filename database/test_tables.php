<?php
/**
 * Test if tables exist
 */

include 'config.php';

echo "Testing database tables...\n";

try {
    // Check if crop_schedules table exists
    $result = $conn->query("SHOW TABLES LIKE 'crop_schedules'");
    if ($result->num_rows > 0) {
        echo "✓ crop_schedules table exists\n";
    } else {
        echo "✗ crop_schedules table does not exist\n";
    }
    
    // Check if crop_feedback table exists
    $result = $conn->query("SHOW TABLES LIKE 'crop_feedback'");
    if ($result->num_rows > 0) {
        echo "✓ crop_feedback table exists\n";
    } else {
        echo "✗ crop_feedback table does not exist\n";
    }
    
    // Show all tables
    echo "\nAll tables in database:\n";
    $result = $conn->query("SHOW TABLES");
    while ($row = $result->fetch_array()) {
        echo "- " . $row[0] . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$conn->close();
?>
