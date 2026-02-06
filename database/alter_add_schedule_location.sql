-- ALTER TABLE script to add location column to crop_schedules table
-- Run this script so the schedule stores the selected location (from recommendations) instead of relying on user profile.

-- Add location column to crop_schedules table (VARCHAR to match "Barangay, Barbaza, Antique" format)
ALTER TABLE crop_schedules 
ADD COLUMN location VARCHAR(255) NULL AFTER recommendation_id;

-- Note: The column is NULL to allow existing records to remain valid.
-- New schedules will store the location chosen when adding from Crop Recommendations.
-- get_schedule_details and reports will prefer this value over location from recommendation/weather_data.
