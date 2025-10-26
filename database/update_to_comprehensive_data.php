<?php
/**
 * Update script to replace existing crop data with comprehensive dataset
 * This script updates the database with all 50 crops from the Barbaza, Antique dataset
 */

include 'config.php';

echo "<h2>Updating Crop Recommendation Database with Comprehensive Dataset...</h2>";

// Check if we should proceed with the update
$check_existing = "SELECT COUNT(*) as count FROM crops";
$result = $conn->query($check_existing);
$existing_count = $result->fetch_assoc()['count'];

if ($existing_count > 0) {
    echo "<p style='color: orange;'>Warning: Found $existing_count existing crops. This will replace all existing data.</p>";
    echo "<p>Proceeding with update...</p>";
}

// Read and execute the update SQL file
$sql_file = 'update_crop_data.sql';
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
    
    echo "<p style='color: green;'>Database update completed!</p>";
    echo "<p>Successfully executed: $success_count statements</p>";
    if ($error_count > 0) {
        echo "<p style='color: red;'>Errors: $error_count statements</p>";
    }
    
    // Show updated data counts
    echo "<h3>Updated Data Counts:</h3>";
    $tables_to_check = ['soil_types', 'crops', 'crop_soil_compatibility'];
    foreach ($tables_to_check as $table) {
        $count_sql = "SELECT COUNT(*) as count FROM $table";
        $result = $conn->query($count_sql);
        if ($row = $result->fetch_assoc()) {
            echo "<p>$table: {$row['count']} records</p>";
        }
    }
    
    // Show sample of new crops
    echo "<h3>Sample of New Crops:</h3>";
    $sample_sql = "SELECT name, marketability, soil_type_preference FROM crops ORDER BY name LIMIT 10";
    $result = $conn->query($sample_sql);
    while ($row = $result->fetch_assoc()) {
        echo "<p><strong>{$row['name']}</strong> - {$row['marketability']}</p>";
    }
    
} else {
    echo "<p style='color: red;'>Error: update_crop_data.sql file not found!</p>";
}

echo "<hr>";
echo "<h3>New Features Added:</h3>";
echo "<ul>";
echo "<li><strong>50 Crops</strong> - Complete dataset from Barbaza, Antique</li>";
echo "<li><strong>15 Soil Types</strong> - Specific to Antique region</li>";
echo "<li><strong>Marketability Scoring</strong> - Based on market demand and scope</li>";
echo "<li><strong>Enhanced Season Logic</strong> - Wet season (May-Nov), Dry season (Dec-Apr)</li>";
echo "<li><strong>Soil-Specific Recommendations</strong> - Based on actual soil types in Antique</li>";
echo "</ul>";

echo "<h3>Market Categories Included:</h3>";
echo "<ul>";
echo "<li>Export & National markets (highest priority)</li>";
echo "<li>National markets</li>";
echo "<li>Provincial markets</li>";
echo "<li>Local markets</li>";
echo "<li>High demand crops</li>";
echo "</ul>";

echo "<p><a href='../client/recommendations.php' class='btn btn-primary'>Test Updated Recommendations</a></p>";

$conn->close();
?>
