-- Add preferences table to store user preferences persistently
-- Run this to add preferences functionality

USE plotoryx;

-- Create preferences table
CREATE TABLE IF NOT EXISTS user_preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    preference_key VARCHAR(255) NOT NULL,
    preference_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_preference (user_id, preference_key)
);

-- Insert default preferences for existing users
INSERT IGNORE INTO user_preferences (user_id, preference_key, preference_value)
SELECT id, 'default_entry_type', 'manhwa' FROM users;

INSERT IGNORE INTO user_preferences (user_id, preference_key, preference_value)
SELECT id, 'items_per_page', '12' FROM users;

INSERT IGNORE INTO user_preferences (user_id, preference_key, preference_value)
SELECT id, 'theme', 'light' FROM users;

INSERT IGNORE INTO user_preferences (user_id, preference_key, preference_value)
SELECT id, 'show_ratings', '1' FROM users;

INSERT IGNORE INTO user_preferences (user_id, preference_key, preference_value)
SELECT id, 'auto_save', '0' FROM users;