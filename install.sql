CREATE TABLE IF NOT EXISTS `modx_leads` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(100) NOT NULL,
  `message` TEXT NULL,
  `api_status` VARCHAR(50) DEFAULT 'pending',
  `api_response` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);