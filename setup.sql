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
    email_verified BOOLEAN DEFAULT FALSE,
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
    date_started DATE,
    date_ended DATE,
    link VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample data for testing
INSERT INTO users (name, email, password, email_verified) VALUES
('Test User', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE); -- password: password

INSERT INTO entries (user_id, type, title, status, rating, remarks, favorite) VALUES
(1, 'manhwa', 'Solo Leveling', 'ongoing', 9, 'Amazing story and art!', TRUE),
(1, 'manhwa', 'Tower of God', 'ongoing', 8, 'Great world-building', FALSE),
(1, 'manhwa', 'The God of High School', 'completed', 7, 'Action-packed but predictable', TRUE),
(1, 'movie', 'Inception', 'completed', 10, 'Mind-bending masterpiece', TRUE),
(1, 'movie', 'The Dark Knight', 'completed', 9, 'Best superhero movie ever', FALSE),
(1, 'movie', 'Interstellar', 'completed', 8, 'Beautiful and emotional', TRUE);