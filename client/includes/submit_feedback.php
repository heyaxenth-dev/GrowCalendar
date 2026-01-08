<?php
/**
 * Submit Feedback Handler
 * Handles user feedback submission for crop performance
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
$schedule_id = $_POST['schedule_id'] ?? null;
$crop_condition = $_POST['crop_condition'] ?? null;
$challenges = $_POST['challenges'] ?? [];
$feedback_score = $_POST['feedback_score'] ?? null;
$remarks = $_POST['remarks'] ?? '';

// Validate required fields
if (!$schedule_id || !$crop_condition) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    // Get user's name (technologist name)
    $user_query = "SELECT firstname, lastname FROM users WHERE id = ?";
    $user_stmt = $conn->prepare($user_query);
    $user_stmt->bind_param("i", $_SESSION['user_id']);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    
    if ($user_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }
    
    $user = $user_result->fetch_assoc();
    $technologist_name = trim($user['firstname'] . ' ' . $user['lastname']);
    
    // Verify the schedule belongs to the user and get crop info with farmer name
    $verify_query = "SELECT cs.*, c.id as crop_id, c.name as crop_name 
                     FROM crop_schedules cs 
                     JOIN crops c ON cs.crop_id = c.id 
                     WHERE cs.id = ? AND cs.user_id = ?";
    $stmt = $conn->prepare($verify_query);
    $stmt->bind_param("ii", $schedule_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Schedule not found or access denied']);
        exit;
    }
    
    $schedule = $result->fetch_assoc();
    
    // Get farmer name from schedule
    $farmer_name = null;
    
    // Check if farmer_name column exists in crop_schedules
    $check_column = "SELECT COUNT(*) as count 
                     FROM INFORMATION_SCHEMA.COLUMNS 
                     WHERE TABLE_SCHEMA = DATABASE() 
                     AND TABLE_NAME = 'crop_schedules' 
                     AND COLUMN_NAME = 'farmer_name'";
    $column_check = $conn->query($check_column);
    $has_farmer_name_column = $column_check->fetch_assoc()['count'] > 0;
    
    if ($has_farmer_name_column && !empty($schedule['farmer_name'])) {
        $farmer_name = trim($schedule['farmer_name']);
    } elseif (!empty($schedule['notes']) && preg_match('/^Farmer:\s*(.+?)(\n\n|$)/i', $schedule['notes'], $matches)) {
        // Extract from notes if column doesn't exist
        $farmer_name = trim($matches[1]);
    }
    
    // Convert challenges array to JSON
    $challenges_json = json_encode($challenges);
    
    // Check if farmer_name column exists in crop_feedback
    $check_feedback_column = "SELECT COUNT(*) as count 
                              FROM INFORMATION_SCHEMA.COLUMNS 
                              WHERE TABLE_SCHEMA = DATABASE() 
                              AND TABLE_NAME = 'crop_feedback' 
                              AND COLUMN_NAME = 'farmer_name'";
    $feedback_column_check = $conn->query($check_feedback_column);
    $has_feedback_farmer_column = $feedback_column_check->fetch_assoc()['count'] > 0;
    
    // Insert feedback with technologist name in remarks if not already included
    $remarks_with_name = $remarks;
    if (!empty($technologist_name) && !empty($remarks)) {
        $remarks_with_name = "Technologist: " . $technologist_name . "\n\n" . $remarks;
    } elseif (!empty($technologist_name)) {
        $remarks_with_name = "Technologist: " . $technologist_name;
    }
    
    // Insert feedback
    if ($has_feedback_farmer_column) {
        $insert_query = "INSERT INTO crop_feedback (user_id, crop_schedule_id, recommendation_id, farmer_name, crop_condition, challenges_encountered, remarks, feedback_score) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("iiissssi", 
            $_SESSION['user_id'], 
            $schedule_id, 
            $schedule['recommendation_id'],
            $farmer_name,
            $crop_condition, 
            $challenges_json, 
            $remarks_with_name, 
            $feedback_score
        );
    } else {
        // Fallback: include farmer name in remarks if column doesn't exist
        if (!empty($farmer_name) && !empty($remarks_with_name)) {
            $remarks_with_name = "Farmer: " . $farmer_name . "\n\n" . $remarks_with_name;
        } elseif (!empty($farmer_name)) {
            $remarks_with_name = "Farmer: " . $farmer_name . "\n\n" . $remarks_with_name;
        }
        
        $insert_query = "INSERT INTO crop_feedback (user_id, crop_schedule_id, recommendation_id, crop_condition, challenges_encountered, remarks, feedback_score) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("iiisssi", 
            $_SESSION['user_id'], 
            $schedule_id, 
            $schedule['recommendation_id'], 
            $crop_condition, 
            $challenges_json, 
            $remarks_with_name, 
            $feedback_score
        );
    }
    
    if ($stmt->execute()) {
        // Update the schedule status to completed if not already
        if ($schedule['status'] !== 'completed') {
            $update_query = "UPDATE crop_schedules SET status = 'completed' WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("i", $schedule_id);
            $stmt->execute();
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Feedback submitted successfully'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to submit feedback']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>