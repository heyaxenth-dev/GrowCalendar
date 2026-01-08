<?php
/**
 * Add Farmer Name Column to Crop Feedback Table
 * This script adds the farmer_name column to the crop_feedback table
 */

// Include database configuration
include 'config.php';

echo "<h2>Adding Farmer Name Column to Crop Feedback Table</h2>";

// Check if column already exists
$check_column = "SELECT COUNT(*) as count 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = DATABASE() 
                 AND TABLE_NAME = 'crop_feedback' 
                 AND COLUMN_NAME = 'farmer_name'";

$result = $conn->query($check_column);
$column_exists = $result->fetch_assoc()['count'] > 0;

if ($column_exists) {
    echo "<p style='color: orange;'>⚠ The farmer_name column already exists in the crop_feedback table.</p>";
    echo "<p>No changes needed.</p>";
} else {
    // Read and execute the ALTER TABLE statement
    $sql_file = __DIR__ . '/alter_add_farmer_name_feedback.sql';
    
    if (file_exists($sql_file)) {
        $sql = file_get_contents($sql_file);
        
        // Remove comments and split into statements
        $statements = array_filter(
            array_map('trim', explode(';', $sql)),
            function($stmt) {
                return !empty($stmt) && 
                       !preg_match('/^--/', $stmt) && 
                       !preg_match('/^\/\*/', $stmt) &&
                       !preg_match('/^\*/', $stmt);
            }
        );
        
        $success_count = 0;
        $error_count = 0;
        
        foreach ($statements as $statement) {
            if (!empty(trim($statement))) {
                try {
                    if ($conn->query($statement)) {
                        $success_count++;
                        echo "<p style='color: green;'>✓ Successfully executed: " . htmlspecialchars(substr($statement, 0, 50)) . "...</p>";
                    } else {
                        $error_count++;
                        echo "<p style='color: red;'>✗ Error: " . $conn->error . "</p>";
                    }
                } catch (Exception $e) {
                    $error_count++;
                    echo "<p style='color: red;'>✗ Exception: " . $e->getMessage() . "</p>";
                }
            }
        }
        
        echo "<hr>";
        echo "<h3>Summary:</h3>";
        echo "<p>Successfully executed: <strong>$success_count</strong> statement(s)</p>";
        if ($error_count > 0) {
            echo "<p style='color: red;'>Errors: <strong>$error_count</strong> statement(s)</p>";
        } else {
            echo "<p style='color: green;'><strong>✓ Column added successfully!</strong></p>";
            echo "<p>The farmer_name column has been added to the crop_feedback table.</p>";
        }
    } else {
        echo "<p style='color: red;'>Error: SQL file not found: $sql_file</p>";
    }
}

// Show table structure
echo "<hr>";
echo "<h3>Current crop_feedback Table Structure:</h3>";
$describe_query = "DESCRIBE crop_feedback";
$result = $conn->query($describe_query);

if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse;'>";
    echo "<tr style='background-color: #f0f0f0;'>";
    echo "<th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th>";
    echo "</tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

$conn->close();
?>