<?php
/**
 * Setup script for GrowCalendar Crop Recommendation System
 * Run this script to create the necessary database tables and insert sample data
 */

include 'config.php';

// Check if tables already exist
$tables_exist = true;
$required_tables = ['soil_types', 'crops', 'crop_soil_compatibility', 'weather_data', 'user_soil_preferences', 'crop_recommendations'];

foreach ($required_tables as $table) {
    $check_sql = "SHOW TABLES LIKE '$table'";
    $result = $conn->query($check_sql);
    if ($result->num_rows == 0) {
        $tables_exist = false;
        break;
    }
}

if (!$tables_exist) {
    echo "<h2>Setting up Crop Recommendation Database...</h2>";
    
    // Read and execute the SQL file
    $sql_file = 'create_tables.sql';
    if (file_exists($sql_file)) {
        $sql = file_get_contents($sql_file);
        
        // Split SQL into individual statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        $success_count = 0;
        $error_count = 0;
        
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                if ($conn->query($statement)) {
                    $success_count++;
                } else {
                    $error_count++;
                    echo "<p style='color: red;'>Error: " . $conn->error . "</p>";
                }
            }
        }
        
        echo "<p style='color: green;'>Database setup completed!</p>";
        echo "<p>Successfully executed: $success_count statements</p>";
        if ($error_count > 0) {
            echo "<p style='color: red;'>Errors: $error_count statements</p>";
        }
        
        // Verify tables were created
        echo "<h3>Created Tables:</h3>";
        foreach ($required_tables as $table) {
            $check_sql = "SHOW TABLES LIKE '$table'";
            $result = $conn->query($check_sql);
            if ($result->num_rows > 0) {
                echo "<p style='color: green;'>✓ $table</p>";
            } else {
                echo "<p style='color: red;'>✗ $table (missing)</p>";
            }
        }
        
        // Show sample data counts
        echo "<h3>Sample Data Inserted:</h3>";
        $tables_to_check = ['soil_types', 'crops', 'crop_soil_compatibility'];
        foreach ($tables_to_check as $table) {
            $count_sql = "SELECT COUNT(*) as count FROM $table";
            $result = $conn->query($count_sql);
            if ($row = $result->fetch_assoc()) {
                echo "<p>$table: {$row['count']} records</p>";
            }
        }
        
    } else {
        echo "<p style='color: red;'>Error: create_tables.sql file not found!</p>";
    }
} else {
    echo "<h2>Database tables already exist!</h2>";
    echo "<p>All required tables are present:</p>";
    foreach ($required_tables as $table) {
        echo "<p style='color: green;'>✓ $table</p>";
    }
}

// Show current data counts
echo "<h3>Current Data Counts:</h3>";
$tables_to_check = ['soil_types', 'crops', 'crop_soil_compatibility', 'weather_data', 'user_soil_preferences', 'crop_recommendations'];
foreach ($tables_to_check as $table) {
    $count_sql = "SELECT COUNT(*) as count FROM $table";
    $result = $conn->query($count_sql);
    if ($row = $result->fetch_assoc()) {
        echo "<p>$table: {$row['count']} records</p>";
    }
}

echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Get a free API key from <a href='https://openweathermap.org/api' target='_blank'>OpenWeatherMap</a></li>";
echo "<li>Update the API key in client/includes/weather_api.php (line 35 in recommendations.php)</li>";
echo "<li>Test the recommendation system by visiting the recommendations page</li>";
echo "</ol>";

echo "<p><a href='../client/recommendations.php' class='btn btn-primary'>Go to Recommendations Page</a></p>";

$conn->close();
?>