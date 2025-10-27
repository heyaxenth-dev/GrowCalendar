<?php
require_once 'weather_config.php';
require_once 'weather_api.php';

// Initialize weather API (optional, for future live data)
$weather_api = new WeatherAPI(WEATHER_API_KEY);

// For now, use fallback + sample 7-day forecast
$current_weather = getFallbackWeatherData('Barbaza, Antique, Philippines');

// Static demo 7-day forecast (can later be replaced with real data)
$forecast_data = [
    ['day' => 'Mon', 'high' => 31, 'low' => 24, 'rain_chance' => 20],
    ['day' => 'Tue', 'high' => 32, 'low' => 25, 'rain_chance' => 10],
    ['day' => 'Wed', 'high' => 30, 'low' => 25, 'rain_chance' => 80],
    ['day' => 'Thu', 'high' => 29, 'low' => 24, 'rain_chance' => 60],
    ['day' => 'Fri', 'high' => 30, 'low' => 24, 'rain_chance' => 30],
    ['day' => 'Sat', 'high' => 31, 'low' => 25, 'rain_chance' => 10],
    ['day' => 'Sun', 'high' => 31, 'low' => 25, 'rain_chance' => 0],
];

header('Content-Type: application/json');
echo json_encode([
    'error' => false,
    'location' => $current_weather['location'],
    'forecast' => $forecast_data
]);
?>