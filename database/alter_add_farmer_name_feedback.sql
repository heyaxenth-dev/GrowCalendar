-- ALTER TABLE script to add farmer_name column to crop_feedback table
-- Run this script to add the farmer_name column for storing farmer information in feedback

-- Add farmer_name column to crop_feedback table
ALTER TABLE crop_feedback 
ADD COLUMN farmer_name VARCHAR(255) NULL AFTER crop_schedule_id;

-- Optional: Add index for better query performance if needed
-- CREATE INDEX idx_feedback_farmer_name ON crop_feedback(farmer_name);

-- Note: The column is set to NULL to allow existing records to remain valid
-- You can update existing records if needed by extracting from crop_schedules:
-- UPDATE crop_feedback cf
-- JOIN crop_schedules cs ON cf.crop_schedule_id = cs.id
-- SET cf.farmer_name = cs.farmer_name
-- WHERE cf.farmer_name IS NULL AND cs.farmer_name IS NOT NULL;
