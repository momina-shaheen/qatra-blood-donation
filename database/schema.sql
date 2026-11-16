-- Qatra Database Schema
-- Import this file in phpMyAdmin (or run via MySQL command line)

CREATE DATABASE IF NOT EXISTS qatra_db;
USE qatra_db;

-- ============ ADMIN USERS (session-based authentication) ============
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin login: username = admin / password = admin123
-- ⚠️ Change this password after first login in a real deployment.
INSERT INTO admin_users (username, password) VALUES
('admin', '$2y$10$NopYEgy2yMtIaFGamjF4h.0T/BRzsWETJKxCdU4Jl0H6fC9uRrF36');

-- ============ DONORS ============
CREATE TABLE IF NOT EXISTS donors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    blood_group VARCHAR(5) NOT NULL,
    city VARCHAR(50) NOT NULL,
    area VARCHAR(100) NULL,
    phone VARCHAR(20) NOT NULL,
    age INT NULL,
    donations INT DEFAULT 0,
    available TINYINT(1) DEFAULT 1,
    last_donation DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============ HOSPITALS ============
CREATE TABLE IF NOT EXISTS hospitals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    city VARCHAR(50) NOT NULL,
    type VARCHAR(50) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    verified TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Blood inventory per hospital (separate table = one row per blood group)
CREATE TABLE IF NOT EXISTS hospital_inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hospital_id INT NOT NULL,
    blood_group VARCHAR(5) NOT NULL,
    units INT DEFAULT 0,
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE CASCADE
);

-- ============ BLOOD REQUESTS ============
CREATE TABLE IF NOT EXISTS blood_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient VARCHAR(100) NOT NULL,
    blood_group VARCHAR(5) NOT NULL,
    location VARCHAR(100) NOT NULL,
    hospital VARCHAR(150) NOT NULL,
    units INT DEFAULT 1,
    urgent TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============ BLOOD CAMPS ============
CREATE TABLE IF NOT EXISTS camps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    org VARCHAR(150) NOT NULL,
    city VARCHAR(50) NOT NULL,
    camp_date DATE NOT NULL,
    camp_time VARCHAR(50),
    location VARCHAR(150),
    target_donors INT DEFAULT 0,
    icon VARCHAR(10) DEFAULT '🩸',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============ SAMPLE DATA (optional, for testing) ============
INSERT INTO donors (name, blood_group, city, area, phone, age, donations, available, last_donation) VALUES
('Kamran Shah', 'B+', 'Lahore', 'Model Town', '0300-1234567', 42, 5, 1, '2024-03-10'),
('Ahmed Raza', 'O+', 'Lahore', 'Gulberg', '0301-2345678', 28, 12, 1, '2024-05-01');

INSERT INTO hospitals (name, city, type, phone, verified) VALUES
('PIMS Hospital', 'Islamabad', 'Government hospital', '051-9261170', 1),
('Indus Hospital', 'Karachi', 'Private hospital', '021-35110000', 1);

INSERT INTO camps (org, city, camp_date, camp_time, location, target_donors, icon) VALUES
('Edhi Foundation', 'Karachi', '2026-07-15', '9:00 AM – 4:00 PM', 'Edhi Centre, Saddar, Karachi', 200, '❤️');
