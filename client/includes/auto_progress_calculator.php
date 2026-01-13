<?php
/**
 * Auto Progress Calculator
 * Automatically calculates crop progress based on planting date, expected harvest date, and current date
 */

/**
 * Calculate crop progress percentage automatically
 * @param string $planting_date Planting date (Y-m-d format)
 * @param string $expected_harvest_date Expected harvest date (Y-m-d format)
 * @param string $current_date Current date (Y-m-d format, optional - defaults to today)
 * @return float Progress percentage (0-100)
 */
function calculateAutoProgress($planting_date, $expected_harvest_date, $current_date = null) {
    if (empty($planting_date) || empty($expected_harvest_date)) {
        return 0.0;
    }
    
    if ($current_date === null) {
        $current_date = date('Y-m-d');
    }
    
    $planting = new DateTime($planting_date);
    $harvest = new DateTime($expected_harvest_date);
    $current = new DateTime($current_date);
    
    // Calculate total days
    $total_days = $planting->diff($harvest)->days;
    
    if ($total_days <= 0) {
        return 100.0; // Already past harvest date
    }
    
    // Calculate days elapsed
    $days_elapsed = $planting->diff($current)->days;
    
    // Calculate progress percentage
    $progress = ($days_elapsed / $total_days) * 100;
    
    // Clamp between 0 and 100
    return max(0.0, min(100.0, $progress));
}

/**
 * Auto-determine crop status based on progress and dates
 * @param string $planting_date Planting date (Y-m-d format)
 * @param string $expected_harvest_date Expected harvest date (Y-m-d format)
 * @param float $progress Progress percentage (0-100)
 * @param string $current_status Current status (optional)
 * @return string Status: 'planting', 'vegetative', 'reproductive', 'harvest', 'completed'
 */
function autoDetermineStatus($planting_date, $expected_harvest_date, $progress, $current_status = 'planting') {
    if (empty($planting_date) || empty($expected_harvest_date)) {
        return $current_status;
    }
    
    $current_date = date('Y-m-d');
    $harvest = new DateTime($expected_harvest_date);
    $current = new DateTime($current_date);
    
    // If past harvest date, mark as completed
    if ($current > $harvest) {
        return 'completed';
    }
    
    // Determine status based on progress
    if ($progress >= 90) {
        return 'harvest';
    } elseif ($progress >= 60) {
        return 'reproductive';
    } elseif ($progress >= 30) {
        return 'vegetative';
    } else {
        return 'planting';
    }
}

/**
 * Format date as mm/dd/yy
 * @param string $date Date in Y-m-d format
 * @return string Formatted date as mm/dd/yy
 */
function formatDateMMDDYY($date) {
    if (empty($date)) {
        return '';
    }
    
    $date_obj = new DateTime($date);
    $month = str_pad($date_obj->format('m'), 2, '0', STR_PAD_LEFT);
    $day = str_pad($date_obj->format('d'), 2, '0', STR_PAD_LEFT);
    $year = substr($date_obj->format('Y'), -2);
    
    return $month . '/' . $day . '/' . $year;
}

/**
 * Update crop schedule progress automatically
 * @param mysqli $conn Database connection
 * @param int $schedule_id Schedule ID
 * @return bool Success status
 */
function updateCropScheduleProgress($conn, $schedule_id) {
    // Get schedule data
    $query = "SELECT planting_date, expected_harvest_date, status, progress_percentage 
              FROM crop_schedules 
              WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $schedule_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return false;
    }
    
    $schedule = $result->fetch_assoc();
    
    // Calculate new progress
    $new_progress = calculateAutoProgress(
        $schedule['planting_date'],
        $schedule['expected_harvest_date']
    );
    
    // Determine new status
    $new_status = autoDetermineStatus(
        $schedule['planting_date'],
        $schedule['expected_harvest_date'],
        $new_progress,
        $schedule['status']
    );
    
    // Update database
    $update_query = "UPDATE crop_schedules 
                     SET progress_percentage = ?, 
                         status = ?,
                         updated_at = CURRENT_TIMESTAMP
                     WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("dsi", $new_progress, $new_status, $schedule_id);
    
    return $update_stmt->execute();
}

/**
 * Update all crop schedules progress automatically
 * @param mysqli $conn Database connection
 * @param int $user_id User ID (optional - if provided, only update that user's schedules)
 * @return int Number of schedules updated
 */
function updateAllCropSchedulesProgress($conn, $user_id = null) {
    $query = "SELECT id, planting_date, expected_harvest_date, status, progress_percentage 
              FROM crop_schedules";
    
    if ($user_id !== null) {
        $query .= " WHERE user_id = ?";
    }
    
    $query .= " ORDER BY id";
    
    $stmt = $conn->prepare($query);
    if ($user_id !== null) {
        $stmt->bind_param("i", $user_id);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $updated_count = 0;
    
    while ($schedule = $result->fetch_assoc()) {
        $new_progress = calculateAutoProgress(
            $schedule['planting_date'],
            $schedule['expected_harvest_date']
        );
        
        $new_status = autoDetermineStatus(
            $schedule['planting_date'],
            $schedule['expected_harvest_date'],
            $new_progress,
            $schedule['status']
        );
        
        // Only update if changed
        if (abs($new_progress - $schedule['progress_percentage']) > 0.1 || 
            $new_status !== $schedule['status']) {
            
            $update_query = "UPDATE crop_schedules 
                             SET progress_percentage = ?, 
                                 status = ?,
                                 updated_at = CURRENT_TIMESTAMP
                             WHERE id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("dsi", $new_progress, $new_status, $schedule['id']);
            
            if ($update_stmt->execute()) {
                $updated_count++;
            }
        }
    }
    
    return $updated_count;
}
?>