<?php
/**
 * Insert Sample Feedback Data
 * Uses the correct schedule IDs
 */

include 'config.php';

echo "Inserting sample feedback data...\n";

try {
    // Sample feedback data with correct schedule IDs
    $sample_feedback = [
        [1, 9, 3, 'success', '["adverse_weather"]', 'Tomato crop was very successful. Good weather conditions and proper care resulted in excellent yield.', 5],
        [1, 10, 4, 'partial', '["pests_disease", "adverse_weather"]', 'Eggplant had some pest issues and weather problems. Yield was moderate but could have been better.', 3]
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
    
    echo "\n✓ Sample feedback data insertion completed!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$conn->close();
?>
