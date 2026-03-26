-- UCAPIN Database Schema
-- MySQL 8.0+ / InnoDB Engine
-- Version: 2.0 (Landing Page + Background Removal Update)

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Create Database
CREATE DATABASE IF NOT EXISTS `ucapin` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `ucapin`;

-- ============================================
-- TABLE: users
-- ============================================
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(20),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_email` (`email`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: admins
-- ============================================
CREATE TABLE `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default admin (password: admin123 - CHANGE THIS!)
INSERT INTO `admins` (`username`, `password`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- ============================================
-- TABLE: categories (Text Categories)
-- ============================================
CREATE TABLE `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: template_categories
-- For organizing image templates
-- ============================================
CREATE TABLE `template_categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default Template Categories
INSERT INTO `template_categories` (`name`, `description`) VALUES
('Solid Colors', 'Single color backgrounds'),
('Gradients', 'Gradient color backgrounds'),
('Patterns', 'Pattern-based backgrounds'),
('Seasonal', 'Seasonal templates (Christmas, Eid, etc.)'),
('Eid Mubarak', 'Eid celebration templates'),
('Christmas', 'Christmas celebration templates'),
('New Year', 'New Year celebration templates'),
('Birthday', 'Birthday celebration templates'),
('Motivational', 'Motivational quote backgrounds'),
('Business', 'Professional business templates'),
('Social Media', 'Templates optimized for social media'),
('Minimal', 'Minimal and clean designs');

-- ============================================
-- TABLE: templates
-- ============================================
CREATE TABLE `templates` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(100),
  `file_path` VARCHAR(255) NOT NULL,
  `category_id` INT NULL,
  `thumbnail_path` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_title` (`title`),
  INDEX `idx_category_id` (`category_id`),
  FOREIGN KEY (`category_id`) REFERENCES `template_categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: text_references
-- ============================================
CREATE TABLE `text_references` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT,
  `content` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
  INDEX `idx_category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: images
-- ============================================
CREATE TABLE `images` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `template_id` INT NULL,
  `text_id` INT NULL,
  `original_image` VARCHAR(255),
  `result_image` VARCHAR(255) NOT NULL,
  `custom_text` TEXT,
  `text_position_x` INT DEFAULT 0,
  `text_position_y` INT DEFAULT 0,
  `text_size` INT DEFAULT 24,
  `text_color` VARCHAR(20) DEFAULT '#ffffff',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`template_id`) REFERENCES `templates`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`text_id`) REFERENCES `text_references`(`id`) ON DELETE SET NULL,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: about
-- ============================================
CREATE TABLE `about` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150),
  `content` TEXT,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default About Content
INSERT INTO `about` (`title`, `content`) VALUES
('About UCAPIN', 'UCAPIN is a free online image text generator platform. Create beautiful images with custom text overlays using our easy-to-use editor. Perfect for social media posts, motivational quotes, greeting cards, and more. No design skills needed!');

-- ============================================
-- SAMPLE DATA: Text Categories
-- ============================================
INSERT INTO `categories` (`name`) VALUES
('Motivation'),
('Greetings'),
('Quotes'),
('Announcements'),
('Social Media');

-- ============================================
-- SAMPLE DATA: Text References
-- ============================================
INSERT INTO `text_references` (`category_id`, `content`) VALUES
(1, 'Believe in yourself and all that you are.'),
(1, 'Success is not final, failure is not fatal.'),
(1, 'The only way to do great work is to love what you do.'),
(1, 'Don\'t watch the clock; do what it does. Keep going.'),
(1, 'The future belongs to those who believe in their dreams.'),
(2, 'Happy Birthday! Wishing you all the best.'),
(2, 'Congratulations on your achievement!'),
(2, 'Thank you for your continued support.'),
(2, 'Wishing you joy and happiness.'),
(3, 'Life is what happens when you are busy making other plans.'),
(3, 'The future belongs to those who believe in their dreams.'),
(3, 'In the middle of difficulty lies opportunity.'),
(4, 'Grand Opening - Join Us!'),
(4, 'Special Offer - Limited Time Only!'),
(4, 'Important Announcement - Please Read.'),
(5, 'Follow us for more updates!'),
(5, 'Share this with your friends!'),
(5, 'Tag someone who needs to see this!');

COMMIT;
