-- ============================================================================
-- SSFO eLog Complete Database Setup Script
-- ============================================================================
-- This script creates the complete SSFO database with all tables
-- Run this FIRST before running seeders.sql
-- ============================================================================

-- Drop existing database if needed (uncomment to use)
-- DROP DATABASE IF EXISTS ssfo;

-- Create the database
CREATE DATABASE IF NOT EXISTS ssfo;
USE ssfo;

-- ============================================================================
-- SECTION 1: CORE TABLES
-- ============================================================================

-- Users Table (for all user types: admin, client)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    phone VARCHAR(20) NULL,
    address TEXT NULL,
    profile_image VARCHAR(255) DEFAULT 'public/assets/img/default-avatar.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
);



-- ============================================================================
-- SECTION 2: MAIN REQUESTS TABLE (Master)
-- ============================================================================

CREATE TABLE IF NOT EXISTS requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reference_number VARCHAR(32) UNIQUE NOT NULL,
    user_id INT NULL,
    fullname VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    request_type VARCHAR(50) NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_reference (reference_number),
    INDEX idx_status (status),
    INDEX idx_type (request_type),
    INDEX idx_created (created_at DESC)
);

-- ============================================================================
-- SECTION 3: PROGRAM-SPECIFIC DETAIL TABLES
-- ============================================================================

-- Educational Assistance Details
CREATE TABLE IF NOT EXISTS req_educational (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    age INT,
    sex VARCHAR(20),
    contact VARCHAR(50),
    address TEXT,
    school VARCHAR(255),
    schoolType VARCHAR(50),
    enrollment_path VARCHAR(255),
    registration_path VARCHAR(255),
    indigency_path VARCHAR(255),
    schoolid_path VARCHAR(255),
    validid_path VARCHAR(255),
    grades_path VARCHAR(255),
    statement_path VARCHAR(255),
    FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE CASCADE,
    INDEX idx_request (request_id)
);

-- Medical Assistance Details
CREATE TABLE IF NOT EXISTS req_medical (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    age INT,
    sex VARCHAR(20),
    contact VARCHAR(50),
    address TEXT,
    patientName VARCHAR(255),
    patientAge INT,
    patientSex VARCHAR(20),
    medicalCertificate_path VARCHAR(255),
    barangayIndigency_path VARCHAR(255),
    validId1_path VARCHAR(255),
    validId2_path VARCHAR(255),
    hospitalBill_path VARCHAR(255),
    authorization_path VARCHAR(255),
    letterRequest_path VARCHAR(255),
    socialCaseStudy_path VARCHAR(255),
    FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE CASCADE,
    INDEX idx_request (request_id)
);

-- Burial Assistance Details
CREATE TABLE IF NOT EXISTS req_burial (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    age INT,
    sex VARCHAR(20),
    contact VARCHAR(50),
    address TEXT,
    deceasedName VARCHAR(255),
    deceasedAge INT,
    deceasedSex VARCHAR(20),
    dateOfDeath DATE,
    placeOfDeath VARCHAR(255),
    causeOfDeath VARCHAR(255),
    deathCertificate_path VARCHAR(255),
    barangayIndigency_path VARCHAR(255),
    validId_path VARCHAR(255),
    letterRequest_path VARCHAR(255),
    socialCaseStudy_path VARCHAR(255),
    FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE CASCADE,
    INDEX idx_request (request_id)
);

-- Employment Assistance Details
CREATE TABLE IF NOT EXISTS req_employment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    age INT,
    sex VARCHAR(20),
    contact VARCHAR(50),
    address TEXT,
    employmentType VARCHAR(50),
    pds_path VARCHAR(255),
    resume_path VARCHAR(255),
    recommendation_path VARCHAR(255),
    endorsement_path VARCHAR(255),
    FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE CASCADE,
    INDEX idx_request (request_id)
);

-- Transportation Assistance Details
CREATE TABLE IF NOT EXISTS req_transportation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    age INT,
    sex VARCHAR(20),
    contact VARCHAR(50),
    address TEXT,
    purpose VARCHAR(255),
    destination VARCHAR(255),
    travelDate DATE,
    driverName VARCHAR(255),
    driverContact VARCHAR(50),
    driverLicense VARCHAR(255),
    FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE CASCADE,
    INDEX idx_request (request_id)
);

-- Request status audit trail (admin updates)
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



-- ============================================================================
-- SECTION 4: ADMIN-SPECIFIC TABLES
-- ============================================================================

-- Programs Table (Configurable programs managed by admins)
CREATE TABLE IF NOT EXISTS programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(100),
    category VARCHAR(100),
    status ENUM('active', 'inactive') DEFAULT 'active',
    required_documents JSON COMMENT 'JSON array of required document types',
    custom_fields JSON COMMENT 'JSON object of additional fields',
    stats_total INT DEFAULT 0,
    stats_pending INT DEFAULT 0,
    stats_approved INT DEFAULT 0,
    stats_rejected INT DEFAULT 0,
    stats_completed INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_name (name)
);

-- Admin Info Table (Extended admin profile information)
CREATE TABLE IF NOT EXISTS admin_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    department VARCHAR(100),
    position VARCHAR(100),
    phone VARCHAR(20),
    bio TEXT,
    last_activity TIMESTAMP NULL,
    login_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id)
);

-- Audit Logs Table (Track all admin actions)
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    entity_type VARCHAR(50) COMMENT 'requests, users, programs, etc.',
    entity_id INT,
    action VARCHAR(50) COMMENT 'create, update, delete, approve, reject',
    old_values JSON,
    new_values JSON,
    remarks TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_admin (admin_id),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_created (created_at DESC)
);

-- Program Stats Cache Table (For faster dashboard queries)
CREATE TABLE IF NOT EXISTS program_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    program_id INT NOT NULL,
    date DATE NOT NULL,
    submissions_count INT DEFAULT 0,
    approvals_count INT DEFAULT 0,
    rejections_count INT DEFAULT 0,
    completions_count INT DEFAULT 0,
    avg_approval_days DECIMAL(5,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_program_date (program_id, date),
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE,
    INDEX idx_date (date)
);

-- ============================================================================
-- SECTION 5: TABLE SUMMARY
-- ============================================================================
/*
CORE TABLES:
  - users: All user accounts (admin and client)
  - requests: Master request/application records

PROGRAM DETAIL TABLES:
  - req_educational: Educational assistance details
  - req_medical: Medical assistance details
  - req_burial: Burial assistance details
  - req_employment: Employment assistance details
  - req_transportation: Transportation assistance details

ADMIN TABLES:
  - programs: Configurable program definitions
  - admin_info: Extended admin information
  - audit_logs: Admin action audit trail
  - program_stats: Program statistics cache

TOTAL: 11 tables
*/

-- ============================================================================
-- SECTION 5: NEW FEATURES
-- ============================================================================

CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    priority ENUM('Normal', 'High', 'Urgent') DEFAULT 'Normal',
    audience ENUM('All Beneficiaries', 'Approved Applicants', 'Pending Review') DEFAULT 'All Beneficiaries',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_priority (priority),
    INDEX idx_created (created_at DESC)
);

CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    message VARCHAR(255) NOT NULL,
    link VARCHAR(255) NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_is_read (is_read),
    INDEX idx_created (created_at DESC)
);

CREATE TABLE IF NOT EXISTS inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255),
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created (created_at DESC),
    INDEX idx_is_read (is_read)
);

-- ============================================================================
-- VERIFICATION QUERY
-- ============================================================================
SHOW TABLES;
SELECT COUNT(*) as total_tables FROM information_schema.tables WHERE table_schema = 'ssfo';
