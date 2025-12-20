-- SQL to fix passport fields in reservation_passengers table
-- Run this in your MySQL database to make passport fields nullable

ALTER TABLE `reservation_passengers` 
MODIFY COLUMN `passport_number` VARCHAR(255) NULL,
MODIFY COLUMN `passport_expiry_date` DATE NULL;

-- Verify the changes
DESCRIBE `reservation_passengers`;
