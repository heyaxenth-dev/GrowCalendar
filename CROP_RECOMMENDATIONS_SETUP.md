# GrowCalendar Crop Recommendation System Setup

This guide will help you set up the crop recommendation system for your GrowCalendar application.

## Features

- **Weather Integration**: Fetches real-time weather data from OpenWeatherMap API
- **Soil Analysis**: Considers different soil types and their compatibility with crops
- **Smart Recommendations**: Uses algorithms to match crops with current conditions
- **User Preferences**: Remembers user's soil type and location preferences
- **Fallback System**: Works even when weather API is unavailable

## Database Setup

### 1. Run the Database Setup Script

Navigate to: `http://your-domain/GrowCalendar/database/setup_recommendations.php`

This will:

- Create all necessary database tables
- Insert sample soil types and crops
- Set up crop-soil compatibility data
- Verify the installation

### 2. Manual Database Setup (Alternative)

If the setup script doesn't work, you can manually run the SQL file:

```sql
-- Run the contents of database/create_tables.sql in your MySQL database
```

## Weather API Configuration

### 1. Get a Free API Key

1. Visit [OpenWeatherMap](https://openweathermap.org/api)
2. Sign up for a free account
3. Get your API key from the dashboard

### 2. Configure the API Key

Edit `client/includes/weather_config.php`:

```php
define('WEATHER_API_KEY', 'your_actual_api_key_here');
```

## Database Tables Created

### Core Tables

- `soil_types` - Different soil types with properties
- `crops` - Crop information with requirements
- `crop_soil_compatibility` - Compatibility scores between crops and soils

### User Data Tables

- `user_soil_preferences` - User's soil type and location
- `weather_data` - Historical weather data
- `crop_recommendations` - Generated recommendations

## Sample Data Included

### Soil Types

- Clay Soil
- Sandy Soil
- Loamy Soil
- Silty Soil
- Peaty Soil
- Chalky Soil

### Crops

- Rice
- Corn
- Tomato
- Eggplant
- Okra
- Sweet Potato
- Cassava
- Squash

## How It Works

### 1. User Input

- User selects their soil type
- User enters their location
- System fetches current weather data

### 2. Analysis

- **Soil Compatibility**: Checks crop-soil compatibility scores
- **Weather Matching**: Compares current weather with crop requirements
- **Season Analysis**: Considers optimal planting seasons

### 3. Scoring Algorithm

- **Soil Score (40%)**: Based on crop-soil compatibility
- **Weather Score (40%)**: Temperature, humidity, rainfall matching
- **Season Score (20%)**: Current season vs. optimal planting season

### 4. Recommendations

- Crops with score > 30% are recommended
- Sorted by compatibility score
- Includes reasons and planting tips

## API Integration

### Weather API Features

- Real-time weather data
- Fallback system when API is unavailable
- Location-based weather fetching
- Data caching in database

### Supported Weather APIs

- OpenWeatherMap (Primary)
- AccuWeather (Future)
- Weatherbit (Future)

## Customization

### Adding New Crops

1. Insert into `crops` table
2. Add compatibility data in `crop_soil_compatibility`
3. Update recommendation algorithm if needed

### Adding New Soil Types

1. Insert into `soil_types` table
2. Add compatibility scores for existing crops
3. Update soil selection dropdown

### Modifying Recommendation Algorithm

Edit `client/includes/recommendation_engine.php`:

- Adjust scoring weights
- Add new factors
- Modify compatibility calculations

## Testing

### 1. Basic Functionality

1. Go to the recommendations page
2. Select a soil type
3. Enter a location
4. Click "Get Recommendations"

### 2. Weather API Test

- With API key: Should fetch real weather data
- Without API key: Should use fallback data

### 3. Database Test

- Check if recommendations are saved
- Verify user preferences are stored
- Confirm weather data is recorded

## Troubleshooting

### Common Issues

1. **Database Connection Error**

   - Check `database/config.php` settings
   - Ensure MySQL is running
   - Verify database exists

2. **Weather API Not Working**

   - Check API key configuration
   - Verify internet connection
   - Check API quota limits

3. **No Recommendations Generated**
   - Check if sample data was inserted
   - Verify soil type selection
   - Check for PHP errors

### Debug Mode

Add this to the top of `recommendations.php` for debugging:

```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## File Structure

```
client/
├── recommendations.php          # Main recommendations page
├── includes/
│   ├── weather_api.php         # Weather API integration
│   ├── weather_config.php      # API configuration
│   └── recommendation_engine.php # Recommendation algorithm
database/
├── config.php                  # Database configuration
├── create_tables.sql          # Database schema
└── setup_recommendations.php  # Setup script
```

## Support

For issues or questions:

1. Check the error logs
2. Verify database setup
3. Test API connectivity
4. Review the code comments

## Future Enhancements

- Machine learning recommendations
- Historical weather analysis
- Crop rotation suggestions
- Market price integration
- Mobile app integration
