-- Traffic Tracker Database Schema

-- Create users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (name, email, password) VALUES ('Demo User', 'admin@yomali.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');


-- Domains table (tenants)
CREATE TABLE domains (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    domain VARCHAR(255) NOT NULL UNIQUE,
    api_key VARCHAR(64) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_domain (domain),
    INDEX idx_api_key (api_key),
    INDEX idx_user_id (user_id)
);

-- Insert demo domain for demo user (API key is SHA256 hashed)
INSERT INTO domains (user_id, domain, api_key) VALUES (1, 'localhost', SHA2('0119e8117599a389782322913f7e1353df3079811f5484b975e005ddd18306a1', 256));


-- Visits table
CREATE TABLE visits (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    domain_id INT NOT NULL,
    url VARCHAR(500) NOT NULL,
    base_url VARCHAR(500) NOT NULL,
    page_title VARCHAR(255),
    visitor_ip VARCHAR(45) NOT NULL,
    user_agent TEXT,
    browser VARCHAR(255),
    os VARCHAR(255),
    device VARCHAR(255),
    visitor_hash VARCHAR(64) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    referrer VARCHAR(500),
    FOREIGN KEY (domain_id) REFERENCES domains(id) ON DELETE CASCADE,
    INDEX idx_domain_timestamp (domain_id, timestamp),
    INDEX idx_domain_url (domain_id, url),
    INDEX idx_visitor_hash (visitor_hash)
);

-- Unique visitors table
CREATE TABLE unique_visitors (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    domain_id INT NOT NULL,
    visitor_hash VARCHAR(64) NOT NULL,
    device VARCHAR(255),
    os VARCHAR(255),
    browser VARCHAR(255),
    first_visit TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_visit TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    total_visits INT DEFAULT 1,
    FOREIGN KEY (domain_id) REFERENCES domains(id) ON DELETE CASCADE,
    UNIQUE KEY unique_domain_visitor (domain_id, visitor_hash),
    INDEX idx_domain_first_visit (domain_id, first_visit)
);

-- Daily statistics table
CREATE TABLE daily_stats (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    domain_id INT NOT NULL,
    date DATE NOT NULL,
    unique_visitors INT DEFAULT 0,
    total_visits INT DEFAULT 0,
    unique_pages INT DEFAULT 0,
    FOREIGN KEY (domain_id) REFERENCES domains(id) ON DELETE CASCADE,
    UNIQUE KEY unique_domain_date (domain_id, date),
    INDEX idx_domain_date (domain_id, date)
);