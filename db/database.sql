-- Create Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Food Log Table
CREATE TABLE IF NOT EXISTS food_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    food_item VARCHAR(255) NOT NULL,
    calories INT NOT NULL,
    log_date DATE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create Exercise Log Table
CREATE TABLE IF NOT EXISTS exercise_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    exercise_type VARCHAR(255) NOT NULL,
    calories_burned INT NOT NULL,
    log_date DATE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create Water Log Table
CREATE TABLE IF NOT EXISTS water_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    water_intake INT NOT NULL,  -- In milliliters
    log_date DATE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create Sleep Log Table
CREATE TABLE IF NOT EXISTS sleep_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    sleep_duration INT NOT NULL,  -- Sleep duration in hours
    sleep_quality VARCHAR(50),    -- Optional quality rating (e.g., Good, Poor)
    log_date DATE,                -- Removed NOT NULL constraint and DEFAULT CURRENT_DATE
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS mindfulness_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    activity_type VARCHAR(100) NOT NULL,
    duration INT NOT NULL,
    notes TEXT,
    log_date DATE DEFAULT (CURRENT_DATE),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create Cycle Log Table
CREATE TABLE IF NOT EXISTS cycle_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    cycle_start_date DATE,
    cycle_end_date DATE,
    symptoms TEXT,   -- Optional field to log symptoms during the cycle
    log_date DATE NOT NULL DEFAULT CURRENT_DATE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create Body Log Table (BMI, BMR, etc.)
CREATE TABLE IF NOT EXISTS body_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    bmi FLOAT NOT NULL,
    bmr FLOAT NOT NULL,
    caloric_use INT NOT NULL COMMENT 'Total daily caloric use',
    log_date DATE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS blood_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    blood_pressure VARCHAR(50),   -- E.g., "120/80 mmHg"
    blood_glucose FLOAT,          -- In mg/dL
    log_date DATE NOT NULL DEFAULT (CURDATE()),  -- CURDATE() is widely supported
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);