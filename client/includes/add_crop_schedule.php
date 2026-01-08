<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

/**
 * Add Crop to Schedule Handler
 * Handles adding selected crops to user's crop schedule
 */

// Include database configuration
include '../../database/config.php';

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get form data
$crop_id = $_POST['crop_id'] ?? null;
$recommendation_id = $_POST['recommendation_id'] ?? null;
$planting_date = $_POST['planting_date'] ?? null;
$farmer_name = $_POST['farmer_name'] ?? '';
$notes = $_POST['notes'] ?? '';

// Validate required fields
if (!$crop_id || !$planting_date) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    // Get crop information to calculate harvest date
    $crop_query = "SELECT harvest_days FROM crops WHERE id = ?";
    $stmt = $conn->prepare($crop_query);
    $stmt->bind_param("i", $crop_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Crop not found']);
        exit;
    }
    
    $crop = $result->fetch_assoc();
    $harvest_days = $crop['harvest_days'];
    
    // Calculate expected harvest date
    $planting_date_obj = new DateTime($planting_date);
    $harvest_date_obj = clone $planting_date_obj;
    $harvest_date_obj->add(new DateInterval('P' . $harvest_days . 'D'));
    $expected_harvest_date = $harvest_date_obj->format('Y-m-d');
    
    // Check if farmer_name column exists in the table
    $check_column = "SELECT COUNT(*) as count 
                     FROM INFORMATION_SCHEMA.COLUMNS 
                     WHERE TABLE_SCHEMA = DATABASE() 
                     AND TABLE_NAME = 'crop_schedules' 
                     AND COLUMN_NAME = 'farmer_name'";
    $column_check = $conn->query($check_column);
    $has_farmer_name_column = $column_check->fetch_assoc()['count'] > 0;
    
    // Prepare farmer name
    $farmer_name_clean = !empty($farmer_name) ? trim($farmer_name) : null;
    
    if ($has_farmer_name_column) {
        // Insert crop schedule with farmer_name column
        $insert_query = "INSERT INTO crop_schedules (user_id, crop_id, recommendation_id, planting_date, expected_harvest_date, farmer_name, status, notes) 
                         VALUES (?, ?, ?, ?, ?, ?, 'planting', ?)";
        
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("iiissss", 
            $_SESSION['user_id'], 
            $crop_id, 
            $recommendation_id, 
            $planting_date, 
            $expected_harvest_date, 
            $farmer_name_clean,
            $notes
        );
    } else {
        // Fallback: Combine farmer name and notes if column doesn't exist
        $combined_notes = '';
        if (!empty($farmer_name_clean)) {
            $combined_notes = "Farmer: " . $farmer_name_clean;
            if (!empty($notes)) {
                $combined_notes .= "\n\n" . $notes;
            }
        } else {
            $combined_notes = $notes;
        }
        
        // Insert crop schedule without farmer_name column (backward compatibility)
        $insert_query = "INSERT INTO crop_schedules (user_id, crop_id, recommendation_id, planting_date, expected_harvest_date, status, notes) 
                         VALUES (?, ?, ?, ?, ?, 'planting', ?)";
        
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("iiisss", 
            $_SESSION['user_id'], 
            $crop_id, 
            $recommendation_id, 
            $planting_date, 
            $expected_harvest_date, 
            $combined_notes
        );
    }
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Crop successfully added to schedule',
            'schedule_id' => $conn->insert_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add crop to schedule']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>