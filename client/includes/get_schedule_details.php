<?php
/**
 * Get Schedule Details
 * Returns schedule details in JSON format for viewing
 */

header('Content-Type: application/json');

// Include database configuration
include '../../database/config.php';
include 'auto_progress_calculator.php';

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Get schedule ID
$schedule_id = $_GET['id'] ?? null;

if (!$schedule_id) {
    echo json_encode(['success' => false, 'message' => 'Schedule ID required']);
    exit;
}

try {
    // Get schedule details, including location (via recommendations & weather) and farmer/user info
    $query = "SELECT 
                cs.*,
                c.name AS crop_name,
                c.scientific_name,
                c.harvest_days,
                wd.location,
                u.firstname,
                u.lastname
              FROM crop_schedules cs
              JOIN crops c ON cs.crop_id = c.id
              LEFT JOIN crop_recommendations cr ON cs.recommendation_id = cr.id
              LEFT JOIN weather_data wd ON cr.weather_data_id = wd.id
              LEFT JOIN users u ON cs.user_id = u.id
              WHERE cs.id = ? AND cs.user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $schedule_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Schedule not found']);
        exit;
    }
    
    $schedule = $result->fetch_assoc();
    
    // Calculate auto progress
    $auto_progress = calculateAutoProgress(
        $schedule['planting_date'],
        $schedule['expected_harvest_date']
    );
    
    // Format dates
    $planting_formatted = formatDateMMDDYY($schedule['planting_date']);
    $expected_formatted = formatDateMMDDYY($schedule['expected_harvest_date']);
    $actual_formatted = $schedule['actual_harvest_date'] ? formatDateMMDDYY($schedule['actual_harvest_date']) : null;

    // Determine farmer name to display: prefer explicit farmer_name, fallback to user full name
    $farmer_name = null;
    if (!empty($schedule['farmer_name'])) {
        $farmer_name = $schedule['farmer_name'];
    } else {
        $full_name = trim(($schedule['firstname'] ?? '') . ' ' . ($schedule['lastname'] ?? ''));
        $farmer_name = $full_name !== '' ? $full_name : null;
    }
    
    // Determine status color
    $status_colors = [
        'planting' => 'success',
        'vegetative' => 'primary',
        'reproductive' => 'warning',
        'harvest' => 'danger',
        'completed' => 'secondary'
    ];
    $status_color = $status_colors[$schedule['status']] ?? 'secondary';
    
    echo json_encode([
        'success' => true,
        'schedule' => [
            'crop_name' => $schedule['crop_name'],
            'scientific_name' => $schedule['scientific_name'],
            'farmer_name' => $farmer_name,
            'location' => $schedule['location'] ?? null,
            'planting_date' => $schedule['planting_date'],
            'planting_date_formatted' => $planting_formatted,
            'expected_harvest_date' => $schedule['expected_harvest_date'],
            'expected_harvest_date_formatted' => $expected_formatted,
            'actual_harvest_date' => $schedule['actual_harvest_date'],
            'actual_harvest_date_formatted' => $actual_formatted,
            'status' => ucfirst($schedule['status']),
            'status_color' => $status_color,
            'progress_percentage' => number_format(max($schedule['progress_percentage'], $auto_progress), 1),
            'harvest_days' => $schedule['harvest_days'],
            'notes' => $schedule['notes'] ?? ''
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>