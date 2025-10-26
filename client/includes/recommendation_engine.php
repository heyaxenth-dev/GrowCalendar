<?php
/**
 * Crop Recommendation Engine for GrowCalendar
 * Analyzes weather and soil conditions to recommend suitable crops
 */

class CropRecommendationEngine {
    private $conn;
    
    public function __construct($database_connection) {
        $this->conn = $database_connection;
    }
    
    /**
     * Generate crop recommendations based on weather and soil data
     * @param int $user_id User ID
     * @param int $soil_type_id Selected soil type
     * @param array $weather_data Current weather conditions
     * @return array Array of recommended crops with scores
     */
    public function generateRecommendations($user_id, $soil_type_id, $weather_data) {
        // Get all crops
        $crops = $this->getAllCrops();
        $recommendations = [];
        
        foreach ($crops as $crop) {
            $score = $this->calculateCropScore($crop, $soil_type_id, $weather_data);
            
            if ($score > 0.3) { // Only recommend crops with score > 30%
                $recommendations[] = [
                    'crop' => $crop,
                    'score' => $score,
                    'reasons' => $this->generateReasons($crop, $soil_type_id, $weather_data, $score),
                    'planting_tips' => $this->generatePlantingTips($crop, $weather_data)
                ];
            }
        }
        
        // Sort by score (highest first)
        usort($recommendations, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        
        // Save recommendations to database
        $this->saveRecommendations($user_id, $recommendations, $soil_type_id, $weather_data);
        
        return $recommendations;
    }
    
    /**
     * Calculate compatibility score for a crop
     * @param array $crop Crop data
     * @param int $soil_type_id Soil type ID
     * @param array $weather_data Weather data
     * @return float Score between 0 and 1
     */
    private function calculateCropScore($crop, $soil_type_id, $weather_data) {
        $soil_score = $this->calculateSoilScore($crop['id'], $soil_type_id);
        $weather_score = $this->calculateWeatherScore($crop, $weather_data);
        $season_score = $this->calculateSeasonScore($crop);
        $marketability_score = $this->calculateMarketabilityScore($crop);
        
        // Weighted average: 30% soil, 30% weather, 20% season, 20% marketability
        $total_score = ($soil_score * 0.3) + ($weather_score * 0.3) + ($season_score * 0.2) + ($marketability_score * 0.2);
        
        return min(1.0, max(0.0, $total_score));
    }
    
    /**
     * Calculate soil compatibility score
     * @param int $crop_id Crop ID
     * @param int $soil_type_id Soil type ID
     * @return float Score between 0 and 1
     */
    private function calculateSoilScore($crop_id, $soil_type_id) {
        $sql = "SELECT compatibility_score FROM crop_soil_compatibility 
                WHERE crop_id = ? AND soil_type_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $crop_id, $soil_type_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return (float)$row['compatibility_score'];
        }
        
        return 0.0; // No compatibility data
    }
    
    /**
     * Calculate weather compatibility score
     * @param array $crop Crop data
     * @param array $weather_data Weather data
     * @return float Score between 0 and 1
     */
    private function calculateWeatherScore($crop, $weather_data) {
        $temperature_score = $this->calculateTemperatureScore($crop, $weather_data['temperature']);
        $humidity_score = $this->calculateHumidityScore($crop, $weather_data['humidity']);
        $rainfall_score = $this->calculateRainfallScore($crop, $weather_data['rainfall']);
        
        return ($temperature_score + $humidity_score + $rainfall_score) / 3;
    }
    
    /**
     * Calculate temperature compatibility
     * @param array $crop Crop data
     * @param float $current_temp Current temperature
     * @return float Score between 0 and 1
     */
    private function calculateTemperatureScore($crop, $current_temp) {
        $min_temp = $crop['temperature_min'];
        $max_temp = $crop['temperature_max'];
        
        if ($current_temp < $min_temp || $current_temp > $max_temp) {
            return 0.0;
        }
        
        $optimal_range = $max_temp - $min_temp;
        
        // Handle case where min and max temperature are the same (zero range)
        if ($optimal_range == 0) {
            return 1.0; // Perfect score if current temperature matches the exact requirement
        }
        
        $distance_from_min = abs($current_temp - $min_temp);
        $distance_from_max = abs($current_temp - $max_temp);
        
        // Score higher for temperatures closer to the middle of the range
        $middle = ($min_temp + $max_temp) / 2;
        $distance_from_middle = abs($current_temp - $middle);
        
        return max(0.0, 1.0 - ($distance_from_middle / ($optimal_range / 2)));
    }
    
    /**
     * Calculate humidity compatibility
     * @param array $crop Crop data
     * @param float $current_humidity Current humidity
     * @return float Score between 0 and 1
     */
    private function calculateHumidityScore($crop, $current_humidity) {
        $min_humidity = $crop['humidity_min'];
        $max_humidity = $crop['humidity_max'];
        
        if ($current_humidity < $min_humidity || $current_humidity > $max_humidity) {
            return 0.0;
        }
        
        $optimal_range = $max_humidity - $min_humidity;
        
        // Handle case where min and max humidity are the same (zero range)
        if ($optimal_range == 0) {
            return 1.0; // Perfect score if current humidity matches the exact requirement
        }
        
        $middle = ($min_humidity + $max_humidity) / 2;
        $distance_from_middle = abs($current_humidity - $middle);
        
        return max(0.0, 1.0 - ($distance_from_middle / ($optimal_range / 2)));
    }
    
    /**
     * Calculate rainfall compatibility
     * @param array $crop Crop data
     * @param float $current_rainfall Current rainfall
     * @return float Score between 0 and 1
     */
    private function calculateRainfallScore($crop, $current_rainfall) {
        $min_rainfall = $crop['rainfall_min'];
        $max_rainfall = $crop['rainfall_max'];
        
        if ($current_rainfall < $min_rainfall || $current_rainfall > $max_rainfall) {
            return 0.0;
        }
        
        $optimal_range = $max_rainfall - $min_rainfall;
        
        // Handle case where min and max rainfall are the same (zero range)
        if ($optimal_range == 0) {
            return 1.0; // Perfect score if current rainfall matches the exact requirement
        }
        
        $middle = ($min_rainfall + $max_rainfall) / 2;
        $distance_from_middle = abs($current_rainfall - $middle);
        
        return max(0.0, 1.0 - ($distance_from_middle / ($optimal_range / 2)));
    }
    
    /**
     * Calculate season compatibility
     * @param array $crop Crop data
     * @return float Score between 0 and 1
     */
    private function calculateSeasonScore($crop) {
        $current_month = date('n');
        $planting_season = $crop['planting_season'];
        
        // Enhanced season matching for Barbaza, Antique climate
        if ($planting_season === 'Wet Season' && ($current_month >= 5 && $current_month <= 11)) {
            return 1.0; // May-Nov is wet season in Antique
        } elseif ($planting_season === 'Dry Season' && ($current_month >= 12 || $current_month <= 4)) {
            return 1.0; // Dec-Apr is dry season
        } elseif ($planting_season === 'Year-round') {
            return 0.9; // High score for year-round crops
        } else {
            return 0.3; // Lower score for off-season crops
        }
    }
    
    /**
     * Calculate marketability score based on market demand
     * @param array $crop Crop data
     * @return float Score between 0 and 1
     */
    private function calculateMarketabilityScore($crop) {
        $marketability = $crop['marketability'] ?? '';
        
        // Score based on market scope and demand
        if (strpos($marketability, 'Export') !== false && strpos($marketability, 'National') !== false) {
            return 1.0; // Highest score for export + national markets
        } elseif (strpos($marketability, 'Export') !== false) {
            return 0.9; // High score for export potential
        } elseif (strpos($marketability, 'National') !== false) {
            return 0.8; // Good score for national markets
        } elseif (strpos($marketability, 'Provincial') !== false) {
            return 0.7; // Moderate score for provincial markets
        } elseif (strpos($marketability, 'Local') !== false) {
            return 0.6; // Basic score for local markets
        } elseif (strpos($marketability, 'high demand') !== false || strpos($marketability, 'High') !== false) {
            return 0.8; // High score for high demand crops
        } else {
            return 0.5; // Default score for unspecified markets
        }
    }
    
    /**
     * Generate reasons for recommendation
     * @param array $crop Crop data
     * @param int $soil_type_id Soil type ID
     * @param array $weather_data Weather data
     * @param float $score Recommendation score
     * @return array Array of reasons
     */
    private function generateReasons($crop, $soil_type_id, $weather_data, $score) {
        $reasons = [];
        
        // Soil compatibility reason
        $soil_score = $this->calculateSoilScore($crop['id'], $soil_type_id);
        if ($soil_score > 0.7) {
            $reasons[] = "Excellent soil compatibility";
        } elseif ($soil_score > 0.5) {
            $reasons[] = "Good soil compatibility";
        }
        
        // Weather reasons
        $temp_score = $this->calculateTemperatureScore($crop, $weather_data['temperature']);
        if ($temp_score > 0.8) {
            $reasons[] = "Ideal temperature conditions";
        }
        
        $humidity_score = $this->calculateHumidityScore($crop, $weather_data['humidity']);
        if ($humidity_score > 0.8) {
            $reasons[] = "Optimal humidity levels";
        }
        
        // Season reason
        $season_score = $this->calculateSeasonScore($crop);
        if ($season_score > 0.8) {
            $reasons[] = "Perfect planting season";
        }
        
        // Marketability reason
        $marketability_score = $this->calculateMarketabilityScore($crop);
        if ($marketability_score > 0.8) {
            $reasons[] = "High market demand and profitability";
        } elseif ($marketability_score > 0.6) {
            $reasons[] = "Good market potential";
        }
        
        return $reasons;
    }
    
    /**
     * Generate planting tips based on crop and weather
     * @param array $crop Crop data
     * @param array $weather_data Weather data
     * @return array Array of planting tips
     */
    private function generatePlantingTips($crop, $weather_data) {
        $tips = [];
        
        // Water requirements tips
        if ($crop['water_requirements'] === 'High') {
            $tips[] = "Ensure adequate irrigation as this crop requires high water";
        } elseif ($crop['water_requirements'] === 'Low') {
            $tips[] = "This crop is drought-tolerant and requires minimal watering";
        }
        
        // Temperature tips
        if ($weather_data['temperature'] < $crop['temperature_min']) {
            $tips[] = "Consider using greenhouses or row covers to maintain temperature";
        } elseif ($weather_data['temperature'] > $crop['temperature_max']) {
            $tips[] = "Provide shade or mulch to protect from excessive heat";
        }
        
        // Humidity tips
        if ($weather_data['humidity'] < $crop['humidity_min']) {
            $tips[] = "Consider misting or using humidity trays to increase moisture";
        }
        
        return $tips;
    }
    
    /**
     * Get all crops from database
     * @return array Array of crop data
     */
    private function getAllCrops() {
        $sql = "SELECT * FROM crops ORDER BY name";
        $result = $this->conn->query($sql);
        
        $crops = [];
        while ($row = $result->fetch_assoc()) {
            $crops[] = $row;
        }
        
        return $crops;
    }
    
    /**
     * Save recommendations to database
     * @param int $user_id User ID
     * @param array $recommendations Array of recommendations
     * @param int $soil_type_id Soil type ID
     * @param array $weather_data Weather data
     */
    private function saveRecommendations($user_id, $recommendations, $soil_type_id, $weather_data) {
        // First, save weather data
        $weather_sql = "INSERT INTO weather_data (location, temperature, humidity, rainfall, wind_speed, weather_condition, api_source) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($weather_sql);
        $stmt->bind_param("sddddss", 
            $weather_data['location'],
            $weather_data['temperature'],
            $weather_data['humidity'],
            $weather_data['rainfall'],
            $weather_data['wind_speed'],
            $weather_data['weather_condition'],
            $weather_data['api_source']
        );
        $stmt->execute();
        $weather_id = $this->conn->insert_id;
        $stmt->close();
        
        // Save each recommendation
        foreach ($recommendations as $rec) {
            $sql = "INSERT INTO crop_recommendations (user_id, crop_id, soil_type_id, weather_data_id, recommendation_score, reasons, planting_tips) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            
            $reasons_text = implode(', ', $rec['reasons']);
            $tips_text = implode(', ', $rec['planting_tips']);
            
            $stmt->bind_param("iiiidss", 
                $user_id,
                $rec['crop']['id'],
                $soil_type_id,
                $weather_id,
                $rec['score'],
                $reasons_text,
                $tips_text
            );
            $stmt->execute();
            $stmt->close();
        }
    }
    
    /**
     * Get user's soil preferences
     * @param int $user_id User ID
     * @return array|false Soil preference data or false
     */
    public function getUserSoilPreference($user_id) {
        $sql = "SELECT st.*, usp.location, usp.notes 
                FROM user_soil_preferences usp 
                JOIN soil_types st ON usp.soil_type_id = st.id 
                WHERE usp.user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Save or update user soil preference
     * @param int $user_id User ID
     * @param int $soil_type_id Soil type ID
     * @param string $location Location
     * @param string $notes Additional notes
     * @return bool Success status
     */
    public function saveUserSoilPreference($user_id, $soil_type_id, $location, $notes = '') {
        // Check if preference exists
        $check_sql = "SELECT id FROM user_soil_preferences WHERE user_id = ?";
        $stmt = $this->conn->prepare($check_sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Update existing preference
            $sql = "UPDATE user_soil_preferences SET soil_type_id = ?, location = ?, notes = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("issi", $soil_type_id, $location, $notes, $user_id);
        } else {
            // Insert new preference
            $sql = "INSERT INTO user_soil_preferences (user_id, soil_type_id, location, notes) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("iiss", $user_id, $soil_type_id, $location, $notes);
        }
        
        $success = $stmt->execute();
        $stmt->close();
        
        return $success;
    }
}
?>
