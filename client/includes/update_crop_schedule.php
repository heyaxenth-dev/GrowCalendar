<?php
/**
 * Update Crop Schedule Handler
 * Handles updating crop schedule status and progress
 */

// Include database configuration
include '../database/config.php';

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
$schedule_id = $_POST['schedule_id'] ?? null;
$new_status = $_POST['new_status'] ?? null;
$progress_percentage = $_POST['progress_percentage'] ?? 0;
$actual_harvest_date = $_POST['actual_harvest_date'] ?? null;
$notes = $_POST['notes'] ?? '';

// Validate required fields
if (!$schedule_id || !$new_status) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    // Verify the schedule belongs to the user
    $verify_query = "SELECT id FROM crop_schedules WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($verify_query);
    $stmt->bind_param("ii", $schedule_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Schedule not found or access denied']);
        exit;
    }
    
    // Update the schedule
    $update_query = "UPDATE crop_schedules SET 
                     status = ?, 
                     progress_percentage = ?, 
                     notes = ?";
    
    $params = [$new_status, $progress_percentage, $notes];
    $types = "sds";
    
    // Add actual harvest date if provided and status is completed
    if ($actual_harvest_date && $new_status === 'completed') {
        $update_query .= ", actual_harvest_date = ?";
        $params[] = $actual_harvest_date;
        $types .= "s";
    }
    
    $update_query .= " WHERE id = ?";
    $params[] = $schedule_id;
    $types .= "i";
    
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Schedule updated successfully'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update schedule']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>
