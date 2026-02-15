-- Manual migration to add timezone support to users table
-- Run this if you cannot run `php artisan migrate` due to PHP version constraints

ALTER TABLE users ADD COLUMN timezone VARCHAR(255) DEFAULT 'Africa/Lagos' AFTER email;

-- Update the migrations table to mark this migration as completed (optional, for tracking)
INSERT INTO migrations (migration, batch) VALUES ('2025_02_15_000000_add_timezone_to_users_table', 1) ON DUPLICATE KEY UPDATE batch = 1;
