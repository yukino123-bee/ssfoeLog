-- Run on existing ssfo database if tables are missing
USE ssfo;

CREATE TABLE IF NOT EXISTS request_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    action_by INT NOT NULL,
    status_from VARCHAR(32) NULL,
    status_to VARCHAR(32) NOT NULL,
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE CASCADE,
    FOREIGN KEY (action_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_request (request_id),
    INDEX idx_created (created_at DESC)
);

-- Guest "apply permission" flow: referral code + PIN after admin approval
CREATE TABLE IF NOT EXISTS client_apply_access (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    referral_code VARCHAR(32) NOT NULL UNIQUE,
    access_pin VARCHAR(12) NULL,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    admin_note TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_status (status)
);
