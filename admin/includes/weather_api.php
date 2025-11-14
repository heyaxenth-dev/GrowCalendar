<?php
/**
 * Weather API Integration for GrowCalendar
 * Fetches current weather data from OpenWeatherMap API
 */

class WeatherAPI {
    private $api_key;
    private $base_url = 'https://api.openweathermap.org/data/2.5/weather';
    private $one_call_url = 'https://api.openweathermap.org/data/2.5/onecall';
    private $forecast_url = 'https://api.openweathermap.org/data/2.5/forecast';
    
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
     * Get 7-day daily forecast for a city using One Call API
     * This method resolves city -> coordinates, then fetches daily forecast
     * @param string $city
     * @param string $country_code
     * @return array ['error'=>false,'daily'=>[...]] or ['error'=>true,'message'=>...]
     */
    public function get7DayForecast($city, $country_code = '') {
        $resolved = $this->resolveLocation($city, $country_code);
        if ($resolved['error']) {
            return $resolved;
        }

        $lat = $resolved['lat'];
        $lon = $resolved['lon'];

        // Attempt One Call API (requires paid plan for some accounts)
        $forecast_url = $this->one_call_url . '?lat=' . $lat . '&lon=' . $lon . '&exclude=minutely,hourly,alerts,current' . '&appid=' . $this->api_key . '&units=metric';
        $forecast_response = $this->fetchJson($forecast_url);

        if ($forecast_response['success'] && isset($forecast_response['data']['daily'])) {
            $days = $this->mapOneCallDaily($forecast_response['data']['daily']);
            if (!empty($days)) {
                return [
                    'error' => false,
                    'city' => $resolved['city'],
                    'country' => $resolved['country'],
                    'daily' => $days,
                    'source' => 'onecall'
                ];
            }
        }

        // Fallback to 5-day/3-hour forecast aggregation (free tier)
        $fallback = $this->getAggregated5DayForecast($lat, $lon, $resolved['city'], $resolved['country']);
        if (!$fallback['error']) {
            return $fallback;
        }

        return [
            'error' => true,
            'message' => $forecast_response['message'] ?? $fallback['message'] ?? 'Unable to fetch forecast data.'
        ];
    }

    /**
     * Resolve a location string into coordinates and canonical names.
     * @param string $city
     * @param string $country_code
     * @return array
     */
    private function resolveLocation($city, $country_code = '') {
        $location = $country_code ? $city . ',' . $country_code : $city;
        $resolve_url = $this->base_url . '?q=' . urlencode($location) . '&appid=' . $this->api_key . '&units=metric';
        $resolve_response = $this->fetchJson($resolve_url);

        if (!$resolve_response['success']) {
            return [
                'error' => true,
                'message' => 'Failed to resolve coordinates. ' . $resolve_response['message']
            ];
        }

        $data = $resolve_response['data'];
        if (!isset($data['coord']['lat'], $data['coord']['lon'])) {
            return [
                'error' => true,
                'message' => 'Coordinates not found for the specified location.'
            ];
        }

        return [
            'error' => false,
            'lat' => $data['coord']['lat'],
            'lon' => $data['coord']['lon'],
            'city' => $data['name'] ?? $city,
            'country' => $data['sys']['country'] ?? $country_code
        ];
    }

    /**
     * Attempt to aggregate the free 5-day/3-hour forecast into daily summaries.
     * Provides up to 7 days by repeating the last available forecast.
     */
    private function getAggregated5DayForecast($lat, $lon, $city, $country) {
        $url = $this->forecast_url . '?lat=' . $lat . '&lon=' . $lon . '&appid=' . $this->api_key . '&units=metric';
        $response = $this->fetchJson($url);
        if (!$response['success'] || !isset($response['data']['list'])) {
            return [
                'error' => true,
                'message' => 'Failed to fetch 5-day forecast. ' . ($response['message'] ?? '')
            ];
        }

        $list = $response['data']['list'];
        $grouped = [];
        foreach ($list as $entry) {
            if (!isset($entry['dt'])) {
                continue;
            }
            $timestamp = (int)$entry['dt'];
            $date = gmdate('Y-m-d', $timestamp);
            if (!isset($grouped[$date])) {
                $grouped[$date] = [
                    'temps' => [],
                    'conditions' => [],
                    'rain_chance_sum' => 0,
                    'rain_count' => 0
                ];
            }
            if (isset($entry['main']['temp_max'])) {
                $grouped[$date]['temps'][] = [
                    'max' => $entry['main']['temp_max'],
                    'min' => $entry['main']['temp_min'] ?? $entry['main']['temp_max']
                ];
            }
            if (isset($entry['weather'][0]['main'])) {
                $condition = $entry['weather'][0]['main'];
                if (!isset($grouped[$date]['conditions'][$condition])) {
                    $grouped[$date]['conditions'][$condition] = 0;
                }
                $grouped[$date]['conditions'][$condition]++;
            }
            if (isset($entry['pop'])) {
                $grouped[$date]['rain_chance_sum'] += $entry['pop'];
                $grouped[$date]['rain_count']++;
            }
        }

        ksort($grouped);
        $dateKeys = array_keys($grouped);
        $days = [];
        foreach ($dateKeys as $date) {
            $info = $grouped[$date];
            if (count($days) >= 7) {
                break;
            }
            $max = null;
            $min = null;
            foreach ($info['temps'] as $temp) {
                $max = is_null($max) ? $temp['max'] : max($max, $temp['max']);
                $min = is_null($min) ? $temp['min'] : min($min, $temp['min']);
            }
            $condition = 'Clear';
            if (!empty($info['conditions'])) {
                arsort($info['conditions']);
                $condition = array_key_first($info['conditions']);
            }
            $icon = $this->mapConditionToIcon($condition);
            $rainChance = $info['rain_count'] > 0 ? round(($info['rain_chance_sum'] / $info['rain_count']) * 100) : 0;

            $days[] = [
                'day' => date('l', strtotime($date)),
                'high' => is_null($max) ? null : round($max),
                'low' => is_null($min) ? null : round($min),
                'condition' => $condition,
                'rain_chance' => $rainChance,
                'icon' => $icon
            ];
        }

        if (empty($days)) {
            return [
                'error' => true,
                'message' => 'No forecast data available.'
            ];
        }

        // Ensure 7 entries by repeating last day if necessary
        $lastDateTimestamp = null;
        if (!empty($dateKeys)) {
            $lastDateTimestamp = strtotime($dateKeys[count($dateKeys) - 1]);
        }
        if ($lastDateTimestamp === false || is_null($lastDateTimestamp)) {
            $lastDateTimestamp = time();
        }

        while (count($days) < 7) {
            $last = $days[count($days) - 1];
            $lastDateTimestamp = strtotime('+1 day', $lastDateTimestamp);
            $lastCopy = $last;
            $lastCopy['day'] = date('l', $lastDateTimestamp);
            $days[] = $lastCopy;
        }

        // Rename the first day's label to Today
        $days[0]['day'] = 'Today';

        return [
            'error' => false,
            'city' => $city,
            'country' => $country,
            'daily' => $days,
            'source' => '5-day'
        ];
    }

    /**
     * Convert One Call API daily payload to the structure used by the UI.
     */
    private function mapOneCallDaily($daily) {
        $days = [];
        $count = 0;
        foreach ($daily as $day) {
            if ($count >= 7) {
                break;
            }
            $count++;
            $dt = isset($day['dt']) ? (int)$day['dt'] : time();
            $condition = isset($day['weather'][0]['main']) ? $day['weather'][0]['main'] : 'Clear';
            $icon = $this->mapConditionToIcon($condition);
            $rainChance = isset($day['pop']) ? round($day['pop'] * 100) : 0;
            $days[] = [
                'day' => date('l', $dt),
                'high' => isset($day['temp']['max']) ? round($day['temp']['max']) : null,
                'low' => isset($day['temp']['min']) ? round($day['temp']['min']) : null,
                'condition' => $condition,
                'rain_chance' => $rainChance,
                'icon' => $icon
            ];
        }

        if (!empty($days)) {
            $days[0]['day'] = 'Today';
        }

        return $days;
    }

    /**
     * Wrapper for curl JSON requests.
     */
    private function fetchJson($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            return [
                'success' => false,
                'message' => 'cURL error: ' . $error
            ];
        }

        if ($http_code !== 200) {
            return [
                'success' => false,
                'message' => 'HTTP error code: ' . $http_code,
                'body' => $response
            ];
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'success' => false,
                'message' => 'Failed to decode JSON: ' . json_last_error_msg()
            ];
        }

        return [
            'success' => true,
            'data' => $data
        ];
    }

    /**
     * Map weather condition to a Bootstrap Icons name used in UI
     * @param string $condition
     * @return string
     */
    private function mapConditionToIcon($condition) {
        $map = [
            'Thunderstorm' => 'cloud-lightning',
            'Drizzle' => 'cloud-drizzle',
            'Rain' => 'cloud-rain',
            'Snow' => 'snow',
            'Clear' => 'sun',
            'Clouds' => 'clouds',
            'Mist' => 'cloud-haze',
            'Smoke' => 'cloud-haze2',
            'Haze' => 'cloud-haze',
            'Dust' => 'cloud-haze2',
            'Fog' => 'cloud-fog',
            'Sand' => 'cloud-haze2',
            'Ash' => 'cloud-haze2',
            'Squall' => 'wind',
            'Tornado' => 'tornado'
        ];
        return isset($map[$condition]) ? $map[$condition] : 'cloud-sun';
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
function getFallbackWeatherData($location = 'Barbaza, Antique, Philippines') {
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