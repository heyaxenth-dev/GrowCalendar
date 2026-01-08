-- ALTER TABLE script to add farmer_name column to crop_schedules table
-- Run this script to add the farmer_name column for storing farmer information separately

-- Add farmer_name column to crop_schedules table
ALTER TABLE crop_schedules 
ADD COLUMN farmer_name VARCHAR(255) NULL AFTER expected_harvest_date;

-- Optional: Add index for better query performance if needed
-- CREATE INDEX idx_farmer_name ON crop_schedules(farmer_name);

-- Note: The column is set to NULL to allow existing records to remain valid
-- You can update existing records if needed:
-- UPDATE crop_schedules SET farmer_name = 'Default Farmer' WHERE farmer_name IS NULL;
