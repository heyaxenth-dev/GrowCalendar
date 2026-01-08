<?php
/**
 * Get Farmer Name from Schedule
 * Returns the farmer name associated with a crop schedule
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

// Get schedule ID
$schedule_id = $_GET['schedule_id'] ?? null;

if (!$schedule_id) {
    echo json_encode(['success' => false, 'message' => 'Schedule ID not provided']);
    exit;
}

try {
    // Check if farmer_name column exists
    $check_column = "SELECT COUNT(*) as count 
                     FROM INFORMATION_SCHEMA.COLUMNS 
                     WHERE TABLE_SCHEMA = DATABASE() 
                     AND TABLE_NAME = 'crop_schedules' 
                     AND COLUMN_NAME = 'farmer_name'";
    $column_check = $conn->query($check_column);
    $has_farmer_name_column = $column_check->fetch_assoc()['count'] > 0;
    
    // Get schedule with farmer name
    if ($has_farmer_name_column) {
        $query = "SELECT farmer_name, notes FROM crop_schedules WHERE id = ? AND user_id = ?";
    } else {
        $query = "SELECT notes FROM crop_schedules WHERE id = ? AND user_id = ?";
    }
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $schedule_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Schedule not found or access denied']);
        exit;
    }
    
    $schedule = $result->fetch_assoc();
    $farmer_name = null;
    
    if ($has_farmer_name_column && !empty($schedule['farmer_name'])) {
        $farmer_name = trim($schedule['farmer_name']);
    } elseif (!empty($schedule['notes']) && preg_match('/^Farmer:\s*(.+?)(\n\n|$)/i', $schedule['notes'], $matches)) {
        // Extract from notes if column doesn't exist
        $farmer_name = trim($matches[1]);
    }
    
    echo json_encode([
        'success' => true,
        'farmer_name' => $farmer_name
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>