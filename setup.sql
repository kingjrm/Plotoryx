-- Database setup for Plotoryx
-- Run this SQL to create the database and tables

CREATE DATABASE IF NOT EXISTS plotoryx;
USE plotoryx;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_picture VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Entries table (for manhwa and movies)
CREATE TABLE entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('manhwa', 'movie') NOT NULL,
    title VARCHAR(255) NOT NULL,
    image VARCHAR(255),
    status ENUM('ongoing', 'completed') DEFAULT 'ongoing',
    rating INT CHECK (rating >= 1 AND rating <= 10),
    remarks TEXT,
    favorite BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);