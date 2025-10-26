<?php
/**
 * Weather API Configuration
 * Update these settings for your weather API integration
 */

// OpenWeatherMap API Configuration
define('WEATHER_API_KEY', '09ad8da82879e9543ad52beb412c45a4'); // Replace with your actual API key
define('WEATHER_API_URL', 'https://api.openweathermap.org/data/2.5/weather');
define('WEATHER_API_TIMEOUT', 30);

// Fallback weather data for when API is unavailable
define('FALLBACK_WEATHER', [
    'location' => 'Antique, Philippines',
    'temperature' => 28.5,
    'humidity' => 75.0,
    'rainfall' => 0.0,
    'wind_speed' => 5.2,
    'weather_condition' => 'Clear',
    'description' => 'Clear sky',
    'pressure' => 1013.25,
    'visibility' => 10000,
    'api_source' => 'Fallback'
]);

// Alternative weather APIs (for future implementation)
define('ALTERNATIVE_APIS', [
    'accuweather' => [
        'name' => 'AccuWeather',
        'url' => 'http://dataservice.accuweather.com/currentconditions/v1/',
        'key_required' => true
    ],
    'weatherbit' => [
        'name' => 'Weatherbit',
        'url' => 'https://api.weatherbit.io/v2.0/current',
        'key_required' => true
    ]
]);

/**
 * Get weather API key
 * @return string API key
 */
function getWeatherApiKey() {
    return WEATHER_API_KEY;
}

/**
 * Check if weather API is configured
 * @return bool True if API key is set
 */
function isWeatherApiConfigured() {
    return !empty(WEATHER_API_KEY) && WEATHER_API_KEY !== 'your_api_key_here';
}

?>