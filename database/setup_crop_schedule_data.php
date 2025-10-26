<?php
/**
 * Setup Crop Schedule Database with Sample Data
 * This script creates sample crop schedules and feedback data for testing
 */

// Include database configuration
include 'config.php';

echo "Setting up crop schedule database with sample data...\n";

try {
    // Create sample crop schedules for different users
    $sample_schedules = [
        [
            'user_id' => 1, // Assuming user ID 1 exists
            'crop_id' => 1, // Rice
            'recommendation_id' => 1,
            'planting_date' => '2024-01-15',
            'expected_harvest_date' => '2024-05-15',
            'status' => 'vegetative',
            'progress_percentage' => 45.0,
            'notes' => 'Rice planted in wet season. Good growth so far.'
        ],
        [
            'user_id' => 1,
            'crop_id' => 2, // Corn
            'recommendation_id' => 2,
            'planting_date' => '2024-02-01',
            'expected_harvest_date' => '2024-05-01',
            'status' => 'reproductive',
            'progress_percentage' => 75.0,
            'notes' => 'Corn showing good reproductive growth. Expecting good yield.'
        ],
        [
            'user_id' => 1,
            'crop_id' => 3, // Tomato
            'recommendation_id' => 3,
            'planting_date' => '2023-12-01',
            'expected_harvest_date' => '2024-02-15',
            'status' => 'completed',
            'progress_percentage' => 100.0,
            'actual_harvest_date' => '2024-02-10',
            'notes' => 'Tomato harvest completed successfully. Good yield achieved.'
        ],
        [
            'user_id' => 1,
            'crop_id' => 4, // Eggplant
            'recommendation_id' => 4,
            'planting_date' => '2023-11-15',
            'expected_harvest_date' => '2024-02-25',
            'status' => 'completed',
            'progress_percentage' => 100.0,
            'actual_harvest_date' => '2024-02-20',
            'notes' => 'Eggplant harvest completed. Some pest issues encountered.'
        ],
        [
            'user_id' => 1,
            'crop_id' => 5, // Okra
            'recommendation_id' => 5,
            'planting_date' => '2024-01-01',
            'expected_harvest_date' => '2024-03-01',
            'status' => 'harvest',
            'progress_percentage' => 90.0,
            'notes' => 'Okra ready for harvest. Good pod development.'
        ]
    ];

    // Insert sample crop schedules
    $schedule_query = "INSERT INTO crop_schedules (user_id, crop_id, recommendation_id, planting_date, expected_harvest_date, actual_harvest_date, status, progress_percentage, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($schedule_query);

    foreach ($sample_schedules as $schedule) {
        $actual_harvest = isset($schedule['actual_harvest_date']) ? $schedule['actual_harvest_date'] : null;
        
        $stmt->bind_param("iiissssds", 
            $schedule['user_id'],
            $schedule['crop_id'],
            $schedule['recommendation_id'],
            $schedule['planting_date'],
            $schedule['expected_harvest_date'],
            $actual_harvest,
            $schedule['status'],
            $schedule['progress_percentage'],
            $schedule['notes']
        );
        
        if ($stmt->execute()) {
            echo "Inserted crop schedule for crop ID: " . $schedule['crop_id'] . "\n";
        } else {
            echo "Error inserting schedule for crop ID: " . $schedule['crop_id'] . " - " . $stmt->error . "\n";
        }
    }

    // Create sample feedback data
    $sample_feedback = [
        [
            'user_id' => 1,
            'crop_schedule_id' => 3, // Tomato
            'recommendation_id' => 3,
            'crop_condition' => 'success',
            'challenges_encountered' => json_encode(['adverse_weather']),
            'remarks' => 'Tomato crop was very successful. Good weather conditions and proper care resulted in excellent yield.',
            'feedback_score' => 5
        ],
        [
            'user_id' => 1,
            'crop_schedule_id' => 4, // Eggplant
            'recommendation_id' => 4,
            'crop_condition' => 'partial',
            'challenges_encountered' => json_encode(['pests_disease', 'adverse_weather']),
            'remarks' => 'Eggplant had some pest issues and weather problems. Yield was moderate but could have been better.',
            'feedback_score' => 3
        ]
    ];

    // Insert sample feedback
    $feedback_query = "INSERT INTO crop_feedback (user_id, crop_schedule_id, recommendation_id, crop_condition, challenges_encountered, remarks, feedback_score) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($feedback_query);

    foreach ($sample_feedback as $feedback) {
        $stmt->bind_param("iiisssi", 
            $feedback['user_id'],
            $feedback['crop_schedule_id'],
            $feedback['recommendation_id'],
            $feedback['crop_condition'],
            $feedback['challenges_encountered'],
            $feedback['remarks'],
            $feedback['feedback_score']
        );
        
        if ($stmt->execute()) {
            echo "Inserted feedback for schedule ID: " . $feedback['crop_schedule_id'] . "\n";
        } else {
            echo "Error inserting feedback for schedule ID: " . $feedback['crop_schedule_id'] . " - " . $stmt->error . "\n";
        }
    }

    echo "Database setup completed successfully!\n";

} catch (Exception $e) {
    echo "Error setting up database: " . $e->getMessage() . "\n";
}

$conn->close();
?>