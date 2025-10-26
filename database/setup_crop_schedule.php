<?php
/**
 * Setup Crop Schedule Tables
 * Creates the crop schedule and feedback tables with sample data
 */

// Include database configuration
include 'config.php';

echo "Setting up crop schedule tables...\n";

try {
    // Read the SQL file
    $sql_file = __DIR__ . '/crop_schedule_tables.sql';
    if (!file_exists($sql_file)) {
        throw new Exception("SQL file not found: $sql_file");
    }
    
    $sql_content = file_get_contents($sql_file);
    
    // Split the SQL content into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql_content)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^--/', $stmt) && strlen(trim($stmt)) > 10;
        }
    );
    
    echo "Found " . count($statements) . " SQL statements to execute...\n";
    
    // Execute each statement
    $success_count = 0;
    $error_count = 0;
    
    foreach ($statements as $index => $statement) {
        if (empty(trim($statement))) continue;
        
        try {
            if ($conn->query($statement)) {
                $success_count++;
                echo "✓ Executed statement " . ($index + 1) . "\n";
            } else {
                $error_count++;
                echo "✗ Error in statement " . ($index + 1) . ": " . $conn->error . "\n";
                echo "Statement: " . substr($statement, 0, 100) . "...\n";
            }
        } catch (Exception $e) {
            $error_count++;
            echo "✗ Exception in statement " . ($index + 1) . ": " . $e->getMessage() . "\n";
        }
    }
    
    echo "\nCrop schedule setup completed!\n";
    echo "Successful statements: $success_count\n";
    echo "Failed statements: $error_count\n";
    
    if ($error_count == 0) {
        echo "\n✓ All crop schedule tables created successfully!\n";
        echo "You can now use the crop schedule and feedback features.\n";
    } else {
        echo "\n⚠ Some statements failed. Please check the errors above.\n";
    }
    
} catch (Exception $e) {
    echo "Error setting up crop schedule tables: " . $e->getMessage() . "\n";
}

$conn->close();
?>
