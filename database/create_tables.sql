-- Database tables for GrowCalendar Crop Recommendation System

-- Soil types table
CREATE TABLE IF NOT EXISTS soil_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    ph_min DECIMAL(3,1),
    ph_max DECIMAL(3,1),
    drainage VARCHAR(50),
    fertility_level VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Crops table
CREATE TABLE IF NOT EXISTS crops (
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
);

-- Crop soil compatibility table
CREATE TABLE IF NOT EXISTS crop_soil_compatibility (
    id INT AUTO_INCREMENT PRIMARY KEY,
    crop_id INT,
    soil_type_id INT,
    compatibility_score DECIMAL(3,2) DEFAULT 0.00,
    notes TEXT,
    FOREIGN KEY (crop_id) REFERENCES crops(id) ON DELETE CASCADE,
    FOREIGN KEY (soil_type_id) REFERENCES soil_types(id) ON DELETE CASCADE,
    UNIQUE KEY unique_crop_soil (crop_id, soil_type_id)
);

-- Weather data table
CREATE TABLE IF NOT EXISTS weather_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location VARCHAR(100) NOT NULL,
    temperature DECIMAL(4,1),
    humidity DECIMAL(4,1),
    rainfall DECIMAL(6,2),
    wind_speed DECIMAL(4,1),
    weather_condition VARCHAR(50),
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    api_source VARCHAR(50)
);

-- User soil preferences table
CREATE TABLE IF NOT EXISTS user_soil_preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    soil_type_id INT,
    location VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (soil_type_id) REFERENCES soil_types(id) ON DELETE CASCADE
);

-- Crop recommendations table
CREATE TABLE IF NOT EXISTS crop_recommendations (
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
);

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

-- Insert sample soil types
INSERT INTO soil_types (name, description, ph_min, ph_max, drainage, fertility_level) VALUES
('Clay Soil', 'Heavy soil with fine particles, good water retention but poor drainage', 6.0, 7.5, 'Poor', 'High'),
('Sandy Soil', 'Light soil with large particles, excellent drainage but poor water retention', 5.5, 7.0, 'Excellent', 'Low'),
('Loamy Soil', 'Balanced soil with good mixture of sand, silt, and clay', 6.0, 7.0, 'Good', 'High'),
('Silty Soil', 'Fine-textured soil with good water retention and moderate drainage', 6.0, 7.5, 'Moderate', 'Medium'),
('Peaty Soil', 'Organic-rich soil with high water retention', 4.0, 6.0, 'Poor', 'Very High'),
('Chalky Soil', 'Alkaline soil with high pH, good drainage', 7.5, 8.5, 'Good', 'Low');

-- Insert sample crops
INSERT INTO crops (name, scientific_name, description, planting_season, harvest_days, water_requirements, temperature_min, temperature_max, humidity_min, humidity_max, rainfall_min, rainfall_max, ph_min, ph_max) VALUES
('Rice', 'Oryza sativa', 'Staple grain crop requiring flooded conditions', 'Wet Season', 120, 'High', 20.0, 35.0, 70.0, 90.0, 1000.0, 2000.0, 5.5, 7.0),
('Corn', 'Zea mays', 'Versatile grain crop for food and feed', 'Dry Season', 90, 'Medium', 15.0, 30.0, 50.0, 80.0, 500.0, 1000.0, 6.0, 7.5),
('Tomato', 'Solanum lycopersicum', 'Popular vegetable crop', 'Dry Season', 75, 'Medium', 18.0, 28.0, 60.0, 80.0, 400.0, 800.0, 6.0, 7.0),
('Eggplant', 'Solanum melongena', 'Tropical vegetable crop', 'Dry Season', 100, 'Medium', 20.0, 30.0, 65.0, 85.0, 500.0, 900.0, 5.5, 7.0),
('Okra', 'Abelmoschus esculentus', 'Heat-tolerant vegetable', 'Dry Season', 60, 'Low', 25.0, 35.0, 50.0, 70.0, 300.0, 600.0, 6.0, 7.5),
('Sweet Potato', 'Ipomoea batatas', 'Root crop with high nutritional value', 'Wet Season', 120, 'Low', 20.0, 30.0, 60.0, 80.0, 600.0, 1200.0, 5.5, 6.5),
('Cassava', 'Manihot esculenta', 'Drought-tolerant root crop', 'Dry Season', 300, 'Low', 20.0, 35.0, 50.0, 80.0, 300.0, 800.0, 5.0, 7.0),
('Squash', 'Cucurbita spp.', 'Versatile gourd family crop', 'Dry Season', 80, 'Medium', 18.0, 28.0, 60.0, 80.0, 400.0, 800.0, 6.0, 7.0);

-- Insert crop-soil compatibility data
INSERT INTO crop_soil_compatibility (crop_id, soil_type_id, compatibility_score, notes) VALUES
-- Rice compatibility
(1, 1, 0.9, 'Rice grows well in clay soil due to water retention'),
(1, 3, 0.8, 'Loamy soil is good for rice cultivation'),
(1, 4, 0.7, 'Silty soil provides good conditions for rice'),

-- Corn compatibility
(2, 3, 0.9, 'Loamy soil is ideal for corn'),
(2, 2, 0.7, 'Sandy soil works but needs more water'),
(2, 4, 0.8, 'Silty soil is good for corn'),

-- Tomato compatibility
(3, 3, 0.9, 'Loamy soil is perfect for tomatoes'),
(3, 4, 0.8, 'Silty soil works well for tomatoes'),
(3, 2, 0.6, 'Sandy soil needs more organic matter'),

-- Eggplant compatibility
(4, 3, 0.9, 'Loamy soil is ideal for eggplant'),
(4, 4, 0.8, 'Silty soil works well'),
(4, 1, 0.7, 'Clay soil can work with good drainage'),

-- Okra compatibility
(5, 3, 0.9, 'Loamy soil is perfect for okra'),
(5, 2, 0.8, 'Sandy soil works well for okra'),
(5, 4, 0.8, 'Silty soil is good for okra'),

-- Sweet Potato compatibility
(6, 2, 0.9, 'Sandy soil is ideal for sweet potato'),
(6, 3, 0.8, 'Loamy soil works well'),
(6, 4, 0.7, 'Silty soil can work with good drainage'),

-- Cassava compatibility
(7, 2, 0.9, 'Sandy soil is perfect for cassava'),
(7, 3, 0.8, 'Loamy soil works well'),
(7, 1, 0.6, 'Clay soil can work with good drainage'),

-- Squash compatibility
(8, 3, 0.9, 'Loamy soil is ideal for squash'),
(8, 4, 0.8, 'Silty soil works well'),
(8, 2, 0.7, 'Sandy soil needs more organic matter');
