-- ==============================================================================
-- Web Technology Assignment II - Database Schema
-- Database Name: student_portal_db
-- Consistent schema used across Question 2 (PHP/MySQL) and Question 3 (Java Servlets/JSP/JDBC)
-- ==============================================================================

CREATE DATABASE IF NOT EXISTS `student_portal_db` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `student_portal_db`;

-- ------------------------------------------------------------------------------
-- Table Structure: users
-- Used for administrative authentication and secure session management in Question 3
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `role` VARCHAR(20) DEFAULT 'admin',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Table Structure: students
-- Used for Student CRUD (Question 2) and Student Portal Display & File Streaming (Question 3)
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `students`;
CREATE TABLE `students` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `roll_number` VARCHAR(30) NOT NULL UNIQUE,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `course` VARCHAR(100) NOT NULL,
    `profile_image` VARCHAR(255) DEFAULT NULL,
    `profile_image_type` VARCHAR(100) DEFAULT NULL,
    `profile_image_size` INT DEFAULT 0,
    `document` VARCHAR(255) DEFAULT NULL,
    `document_type` VARCHAR(100) DEFAULT NULL,
    `document_size` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_roll_number` (`roll_number`),
    INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Seed Data: users
-- Default credentials for testing Question 3 login:
-- Username: admin
-- Password: Admin@123 (SHA-256 hashed: e86f78a8a3caf0b60d8e74e5942aa6d86dc150cd3c03338aef25b7d2d7e3acc7)
-- ------------------------------------------------------------------------------
INSERT INTO `users` (`username`, `password_hash`, `full_name`, `role`) VALUES
('admin', 'e86f78a8a3caf0b60d8e74e5942aa6d86dc150cd3c03338aef25b7d2d7e3acc7', 'System Administrator', 'admin'),
('instructor', 'e86f78a8a3caf0b60d8e74e5942aa6d86dc150cd3c03338aef25b7d2d7e3acc7', 'Course Instructor', 'faculty');


-- ------------------------------------------------------------------------------
-- Seed Data: students
-- Initial sample students for verification and testing
-- ------------------------------------------------------------------------------
INSERT INTO `students` 
(`roll_number`, `full_name`, `email`, `course`, `profile_image`, `profile_image_type`, `profile_image_size`, `document`, `document_type`, `document_size`) 
VALUES
('WT-2026-001', 'Daniel Vance', 'daniel.vance@example.edu', 'Computer Science & Engineering', 'sample_avatar_1.png', 'image/png', 24500, 'dan_resume.pdf', 'application/pdf', 104200),
('WT-2026-002', 'Sophia Chen', 'sophia.chen@example.edu', 'Artificial Intelligence & Data Science', 'sample_avatar_2.png', 'image/png', 28900, 'sophia_cert.pdf', 'application/pdf', 98500),
('WT-2026-003', 'Marcus Thorne', 'marcus.t@example.edu', 'Information Technology', 'sample_avatar_3.png', 'image/png', 21300, 'marcus_assignment.pdf', 'application/pdf', 115000);
