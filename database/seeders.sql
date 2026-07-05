-- SSFO Test Data Seeder
-- Creates basic required data for the application

USE ssfo;

-- Insert sample programs (Default Programs required for the system to function)
INSERT INTO programs (name, description, icon, category, status, required_documents, custom_fields) VALUES
('Educational Assistance', 'Scholarships and school help for students in need', 'book', 'Education', 'active', '["enrollment", "registration", "grades", "validid"]', '{"level": "text", "school": "text"}'),
('Medical Support', 'Healthcare and hospital assistance for medical conditions', 'heartbeat', 'Health', 'active', '["medicalCertificate", "barangayIndigency", "hospitalBill"]', '{"condition": "text", "hospital": "text"}'),
('Burial Assistance', 'Financial help for funeral and burial expenses', 'cross', 'Assistance', 'active', '["deathCertificate", "barangayIndigency"]', '{"deceased": "text", "cause": "text"}'),
('Employment Support', 'Job placement and vocational training assistance', 'briefcase', 'Employment', 'active', '["resume", "pds"]', '{"jobType": "text", "skills": "text"}'),
('Emergency Transportation', 'Travel assistance for urgent medical or family needs', 'bus', 'Transport', 'active', '["validid"]', '{"destination": "text", "purpose": "text"}');

-- Insert sample admin account (password: admin123)
INSERT INTO users (fullname, email, password, role, phone, address, profile_image) VALUES
('Admin User', 'admin@ssfo.local', '$2y$12$.4yLHQEAutFQNN3WQFN0I..s7OdkFzlOnSVGdyHQr2h19aFQSjLjG', 'admin', '09123456789', 'SSFO Office, Main Street', 'assets/img/default-avatar.png');

-- Insert admin info
INSERT INTO admin_info (user_id, department, position, phone) VALUES
(1, 'Administration', 'Administrator', '09123456789');

-- Verify data insertion
SELECT COUNT(*) as total_programs FROM programs;
SELECT COUNT(*) as total_users FROM users;

COMMIT;
