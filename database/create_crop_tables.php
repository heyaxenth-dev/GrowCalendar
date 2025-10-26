<?php
/**
 * Create Crop Schedule Tables
 * Simple approach to create the tables directly
 */

include 'config.php';

echo "Creating crop schedule tables...\n";

try {
    // Create crop_schedules table
    $sql1 = "CREATE TABLE IF NOT EXISTS crop_schedules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        crop_id INT,
        recommendation_id INT,
        planting_date DATE,
        expected_harvest_date DATE,
        actual_harvest_date DATE NULL,
        status ENUM('planting', 'vegetative', 'reproductive', 'harvest', 'completed') DEFAULT 'planting',
        progress_percentage DECIMAL(5,2) DEFAULT 0.00,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (crop_id) REFERENCES crops(id) ON DELETE CASCADE,
        FOREIGN KEY (recommendation_id) REFERENCES crop_recommendations(id) ON DELETE SET NULL
    )";
    
    if ($conn->query($sql1)) {
        echo "✓ crop_schedules table created\n";
    } else {
        echo "✗ Error creating crop_schedules: " . $conn->error . "\n";
    }
    
    // Create crop_feedback table
    $sql2 = "CREATE TABLE IF NOT EXISTS crop_feedback (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        crop_schedule_id INT,
        recommendation_id INT,
        crop_condition ENUM('success', 'partial', 'failure') NOT NULL,
        challenges_encountered JSON,
        remarks TEXT,
        photos JSON,
        feedback_score INT CHECK (feedback_score >= 1 AND feedback_score <= 5),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (crop_schedule_id) REFERENCES crop_schedules(id) ON DELETE CASCADE,
        FOREIGN KEY (recommendation_id) REFERENCES crop_recommendations(id) ON DELETE SET NULL
    )";
    
    if ($conn->query($sql2)) {
        echo "✓ crop_feedback table created\n";
    } else {
        echo "✗ Error creating crop_feedback: " . $conn->error . "\n";
    }
    
    // Insert sample data
    echo "\nInserting sample data...\n";
    
    // Sample crop schedules
    $sample_schedules = [
        [1, 1, 1, '2024-01-15', '2024-05-15', NULL, 'vegetative', 45.0, 'Rice planted in wet season. Good growth so far.'],
        [1, 2, 2, '2024-02-01', '2024-05-01', NULL, 'reproductive', 75.0, 'Corn showing good reproductive growth. Expecting good yield.'],
        [1, 3, 3, '2023-12-01', '2024-02-15', '2024-02-10', 'completed', 100.0, 'Tomato harvest completed successfully. Good yield achieved.'],
        [1, 4, 4, '2023-11-15', '2024-02-25', '2024-02-20', 'completed', 100.0, 'Eggplant harvest completed. Some pest issues encountered.'],
        [1, 5, 5, '2024-01-01', '2024-03-01', NULL, 'harvest', 90.0, 'Okra ready for harvest. Good pod development.']
    ];
    
    $insert_schedule = "INSERT INTO crop_schedules (user_id, crop_id, recommendation_id, planting_date, expected_harvest_date, actual_harvest_date, status, progress_percentage, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_schedule);
    
    foreach ($sample_schedules as $schedule) {
        $stmt->bind_param("iiissssds", 
            $schedule[0], $schedule[1], $schedule[2], $schedule[3], 
            $schedule[4], $schedule[5], $schedule[6], $schedule[7], $schedule[8]
        );
        
        if ($stmt->execute()) {
            echo "✓ Inserted crop schedule for crop ID: " . $schedule[1] . "\n";
        } else {
            echo "✗ Error inserting schedule: " . $stmt->error . "\n";
        }
    }
    
    // Sample feedback data
    $sample_feedback = [
        [1, 3, 3, 'success', '["adverse_weather"]', 'Tomato crop was very successful. Good weather conditions and proper care resulted in excellent yield.', 5],
        [1, 4, 4, 'partial', '["pests_disease", "adverse_weather"]', 'Eggplant had some pest issues and weather problems. Yield was moderate but could have been better.', 3]
    ];
    
    $insert_feedback = "INSERT INTO crop_feedback (user_id, crop_schedule_id, recommendation_id, crop_condition, challenges_encountered, remarks, feedback_score) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_feedback);
    
    foreach ($sample_feedback as $feedback) {
        $stmt->bind_param("iiisssi", 
            $feedback[0], $feedback[1], $feedback[2], $feedback[3], 
            $feedback[4], $feedback[5], $feedback[6]
        );
        
        if ($stmt->execute()) {
            echo "✓ Inserted feedback for schedule ID: " . $feedback[1] . "\n";
        } else {
            echo "✗ Error inserting feedback: " . $stmt->error . "\n";
        }
    }
    
    echo "\n✓ Crop schedule tables setup completed!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$conn->close();
?>
