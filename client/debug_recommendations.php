<?php
// Debug version to check for errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Starting debug...<br>";

// Check database connection
include '../database/config.php';
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
echo "Database connected successfully<br>";

// Check if tables exist
$tables = ['soil_types', 'crops', 'crop_soil_compatibility'];
foreach ($tables as $table) {
    $check = "SHOW TABLES LIKE '$table'";
    $result = $conn->query($check);
    if ($result->num_rows > 0) {
        echo "Table $table exists<br>";
    } else {
        echo "Table $table does NOT exist<br>";
    }
}

// Check soil types
$soil_check = "SELECT COUNT(*) as count FROM soil_types";
$result = $conn->query($soil_check);
if ($result) {
    $row = $result->fetch_assoc();
    echo "Soil types count: " . $row['count'] . "<br>";
} else {
    echo "Error checking soil types: " . $conn->error . "<br>";
}

// Check crops
$crop_check = "SELECT COUNT(*) as count FROM crops";
$result = $conn->query($crop_check);
if ($result) {
    $row = $result->fetch_assoc();
    echo "Crops count: " . $row['count'] . "<br>";
} else {
    echo "Error checking crops: " . $conn->error . "<br>";
}

echo "Debug complete.";
?>