-- Add weather_condition to crop_schedules so reports show the actual weather from when the crop was added (e.g. Clouds), not a wrong value (e.g. Clear) from a mismatched recommendation join.

ALTER TABLE crop_schedules 
ADD COLUMN weather_condition VARCHAR(64) NULL;
