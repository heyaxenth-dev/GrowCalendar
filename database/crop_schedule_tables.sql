-- Crop Schedule Database Tables
-- This file creates the necessary tables for crop scheduling and feedback

-- Crop schedules table for monitoring selected crops
CREATE TABLE IF NOT EXISTS crop_schedules (
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
);

-- Crop feedback table for user feedback on crop performance
CREATE TABLE IF NOT EXISTS crop_feedback (
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
);

-- Insert sample crop schedules
INSERT INTO crop_schedules (user_id, crop_id, recommendation_id, planting_date, expected_harvest_date, actual_harvest_date, status, progress_percentage, notes) VALUES
(1, 1, 1, '2024-01-15', '2024-05-15', NULL, 'vegetative', 45.0, 'Rice planted in wet season. Good growth so far.'),
(1, 2, 2, '2024-02-01', '2024-05-01', NULL, 'reproductive', 75.0, 'Corn showing good reproductive growth. Expecting good yield.'),
(1, 3, 3, '2023-12-01', '2024-02-15', '2024-02-10', 'completed', 100.0, 'Tomato harvest completed successfully. Good yield achieved.'),
(1, 4, 4, '2023-11-15', '2024-02-25', '2024-02-20', 'completed', 100.0, 'Eggplant harvest completed. Some pest issues encountered.'),
(1, 5, 5, '2024-01-01', '2024-03-01', NULL, 'harvest', 90.0, 'Okra ready for harvest. Good pod development.');

-- Insert sample feedback data
INSERT INTO crop_feedback (user_id, crop_schedule_id, recommendation_id, crop_condition, challenges_encountered, remarks, feedback_score) VALUES
(1, 3, 3, 'success', '["adverse_weather"]', 'Tomato crop was very successful. Good weather conditions and proper care resulted in excellent yield.', 5),
(1, 4, 4, 'partial', '["pests_disease", "adverse_weather"]', 'Eggplant had some pest issues and weather problems. Yield was moderate but could have been better.', 3);

-- Create indexes for better performance
CREATE INDEX idx_crop_schedules_user_id ON crop_schedules(user_id);
CREATE INDEX idx_crop_schedules_crop_id ON crop_schedules(crop_id);
CREATE INDEX idx_crop_schedules_status ON crop_schedules(status);
CREATE INDEX idx_crop_schedules_planting_date ON crop_schedules(planting_date);

CREATE INDEX idx_crop_feedback_user_id ON crop_feedback(user_id);
CREATE INDEX idx_crop_feedback_schedule_id ON crop_feedback(crop_schedule_id);
CREATE INDEX idx_crop_feedback_condition ON crop_feedback(crop_condition);
