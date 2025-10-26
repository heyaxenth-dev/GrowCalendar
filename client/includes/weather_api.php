<?php
/**
 * Weather API Integration for GrowCalendar
 * Fetches current weather data from OpenWeatherMap API
 */

class WeatherAPI {
    private $api_key;
    private $base_url = 'https://api.openweathermap.org/data/2.5/weather';
    
    public function __construct($api_key) {
        $this->api_key = $api_key;
    }
    
    /**
     * Get current weather data for a specific location
     * @param string $city City name
     * @param string $country_code Country code (optional)
     * @return array Weather data or error
     */
    public function getCurrentWeather($city, $country_code = '') {
        $location = $country_code ? $city . ',' . $country_code : $city;
        $url = $this->base_url . '?q=' . urlencode($location) . '&appid=' . $this->api_key . '&units=metric';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code === 200) {
            $data = json_decode($response, true);
            return $this->formatWeatherData($data);
        } else {
            return [
                'error' => true,
                'message' => 'Failed to fetch weather data. HTTP Code: ' . $http_code
            ];
        }
    }
    
    /**
     * Get weather by coordinates
     * @param float $lat Latitude
     * @param float $lon Longitude
     * @return array Weather data or error
     */
    public function getWeatherByCoordinates($lat, $lon) {
        $url = $this->base_url . '?lat=' . $lat . '&lon=' . $lon . '&appid=' . $this->api_key . '&units=metric';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code === 200) {
            $data = json_decode($response, true);
            return $this->formatWeatherData($data);
        } else {
            return [
                'error' => true,
                'message' => 'Failed to fetch weather data. HTTP Code: ' . $http_code
            ];
        }
    }
    
    /**
     * Format weather data for database storage
     * @param array $data Raw API response
     * @return array Formatted data
     */
    private function formatWeatherData($data) {
        return [
            'error' => false,
            'location' => $data['name'] . ', ' . $data['sys']['country'],
            'temperature' => round($data['main']['temp'], 1),
            'humidity' => $data['main']['humidity'],
            'rainfall' => isset($data['rain']['1h']) ? $data['rain']['1h'] : 0,
            'wind_speed' => $data['wind']['speed'],
            'weather_condition' => $data['weather'][0]['main'],
            'description' => $data['weather'][0]['description'],
            'pressure' => $data['main']['pressure'],
            'visibility' => isset($data['visibility']) ? $data['visibility'] : null,
            'api_source' => 'OpenWeatherMap'
        ];
    }
    
    /**
     * Save weather data to database
     * @param mysqli $conn Database connection
     * @param array $weather_data Formatted weather data
     * @return int|false Weather data ID or false on error
     */
    public function saveWeatherData($conn, $weather_data) {
        $sql = "INSERT INTO weather_data (location, temperature, humidity, rainfall, wind_speed, weather_condition, api_source) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param("sddddss", 
            $weather_data['location'],
            $weather_data['temperature'],
            $weather_data['humidity'],
            $weather_data['rainfall'],
            $weather_data['wind_speed'],
            $weather_data['weather_condition'],
            $weather_data['api_source']
        );
        
        if ($stmt->execute()) {
            $weather_id = $conn->insert_id;
            $stmt->close();
            return $weather_id;
        } else {
            $stmt->close();
            return false;
        }
    }
}

// Fallback weather data for when API is not available
function getFallbackWeatherData($location = 'Antique, Philippines') {
    return [
        'error' => false,
        'location' => $location,
        'temperature' => 28.5,
        'humidity' => 75.0,
        'rainfall' => 0.0,
        'wind_speed' => 5.2,
        'weather_condition' => 'Clear',
        'description' => 'Clear sky',
        'pressure' => 1013.25,
        'visibility' => 10000,
        'api_source' => 'Fallback'
    ];
}
?>