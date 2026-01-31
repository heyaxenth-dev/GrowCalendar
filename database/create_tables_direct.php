<?php
/**
 * Direct Database Table Creation
 * Creates all necessary tables for the GrowCalendar system
 */

// Include database configuration
include 'config.php';

echo "Creating GrowCalendar database tables...\n";

try {
    // Create soil types table
    $sql = "CREATE TABLE IF NOT EXISTS soil_types (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        ph_min DECIMAL(3,1),
        ph_max DECIMAL(3,1),
        drainage VARCHAR(50),
        fertility_level VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->query($sql);
    echo "✓ Created soil_types table\n";

    // Create crops table
    $sql = "CREATE TABLE IF NOT EXISTS crops (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        scientific_name VARCHAR(150),
        description TEXT,
        planting_season VARCHAR(50),
        harvest_days INT,
        water_requirements VARCHAR(50),
        temperature_min DECIMAL(4,1),
        temperature_max DECIMAL(4,1),
        humidity_min DECIMAL(4,1),
        humidity_max DECIMAL(4,1),
        rainfall_min DECIMAL(6,2),
        rainfall_max DECIMAL(6,2),
        ph_min DECIMAL(3,1),
        ph_max DECIMAL(3,1),
        marketability TEXT,
        soil_type_preference VARCHAR(255),
        weather_conditions TEXT,
        image_url VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->query($sql);
    echo "✓ Created crops table\n";

    // Create crop soil compatibility table
    $sql = "CREATE TABLE IF NOT EXISTS crop_soil_compatibility (
        id INT AUTO_INCREMENT PRIMARY KEY,
        crop_id INT,
        soil_type_id INT,
        compatibility_score DECIMAL(3,2) DEFAULT 0.00,
        notes TEXT,
        FOREIGN KEY (crop_id) REFERENCES crops(id) ON DELETE CASCADE,
        FOREIGN KEY (soil_type_id) REFERENCES soil_types(id) ON DELETE CASCADE,
        UNIQUE KEY unique_crop_soil (crop_id, soil_type_id)
    )";
    $conn->query($sql);
    echo "✓ Created crop_soil_compatibility table\n";

    // Create location-soil mapping (soil types per barangay)
    $sql = "CREATE TABLE IF NOT EXISTS location_soil_types (
        location VARCHAR(150) NOT NULL,
        soil_type_id INT NOT NULL,
        PRIMARY KEY (location, soil_type_id),
        FOREIGN KEY (soil_type_id) REFERENCES soil_types(id) ON DELETE CASCADE
    )";
    $conn->query($sql);
    echo "✓ Created location_soil_types table\n";

    // Create weather data table
    $sql = "CREATE TABLE IF NOT EXISTS weather_data (
        id INT AUTO_INCREMENT PRIMARY KEY,
        location VARCHAR(100) NOT NULL,
        temperature DECIMAL(4,1),
        humidity DECIMAL(4,1),
        rainfall DECIMAL(6,2),
        wind_speed DECIMAL(4,1),
        weather_condition VARCHAR(50),
        recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        api_source VARCHAR(50)
    )";
    $conn->query($sql);
    echo "✓ Created weather_data table\n";

    // Create user soil preferences table
    $sql = "CREATE TABLE IF NOT EXISTS user_soil_preferences (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        soil_type_id INT,
        location VARCHAR(100),
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (soil_type_id) REFERENCES soil_types(id) ON DELETE CASCADE
    )";
    $conn->query($sql);
    echo "✓ Created user_soil_preferences table\n";

    // Create crop recommendations table
    $sql = "CREATE TABLE IF NOT EXISTS crop_recommendations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        crop_id INT,
        soil_type_id INT,
        weather_data_id INT,
        recommendation_score DECIMAL(3,2),
        reasons TEXT,
        planting_tips TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (crop_id) REFERENCES crops(id) ON DELETE CASCADE,
        FOREIGN KEY (soil_type_id) REFERENCES soil_types(id) ON DELETE CASCADE,
        FOREIGN KEY (weather_data_id) REFERENCES weather_data(id) ON DELETE CASCADE
    )";
    $conn->query($sql);
    echo "✓ Created crop_recommendations table\n";

    // Create crop schedules table
    $sql = "CREATE TABLE IF NOT EXISTS crop_schedules (
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
    $conn->query($sql);
    echo "✓ Created crop_schedules table\n";

    // Create crop feedback table
    $sql = "CREATE TABLE IF NOT EXISTS crop_feedback (
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
    $conn->query($sql);
    echo "✓ Created crop_feedback table\n";

    echo "\n✓ All tables created successfully!\n";
    echo "Database setup completed!\n";

} catch (Exception $e) {
    echo "Error creating tables: " . $e->getMessage() . "\n";
}

$conn->close();
?>
