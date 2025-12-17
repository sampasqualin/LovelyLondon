-- Migration: Add tour_type field to tours table
-- Description: Adds support for tour classification (classic/exclusive)
-- Date: 2025-11-12

-- Check if column exists and add if not
ALTER TABLE `tours` ADD COLUMN `tour_type` ENUM('classic', 'exclusive') DEFAULT 'classic' AFTER `slug`;

-- Add index for faster filtering
CREATE INDEX `idx_tour_type` ON `tours`(`tour_type`);

-- Update existing tours to be classic by default
UPDATE `tours` SET `tour_type` = 'classic' WHERE `tour_type` IS NULL;

-- Verify the changes
-- SELECT COUNT(*) as total, tour_type, COUNT(*) as count FROM tours GROUP BY tour_type;
