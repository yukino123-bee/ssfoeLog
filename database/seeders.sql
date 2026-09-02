-- Communifund Assistance System Test Data Seeder
-- Creates basic required data for the application

USE communifund_assistance;

-- Insert sample programs (Default Programs required for the system to function)
INSERT IGNORE INTO programs (name, description, icon, category, status, required_documents, custom_fields) VALUES
('Educational Assistance', 'Scholarships and school help for students in need', 'book', 'Education', 'active', '["enrollment", "registration", "grades", "validid"]', '{"level": "text", "school": "text"}'),
('Medical Support', 'Healthcare and hospital assistance for medical conditions', 'heartbeat', 'Health', 'active', '["medicalCertificate", "barangayIndigency", "hospitalBill"]', '{"condition": "text", "hospital": "text"}'),
('Burial Assistance', 'Financial help for funeral and burial expenses', 'cross', 'Assistance', 'active', '["deathCertificate", "barangayIndigency"]', '{"deceased": "text", "cause": "text"}'),
('Employment Support', 'Job placement and vocational training assistance', 'briefcase', 'Employment', 'active', '["resume", "pds"]', '{"jobType": "text", "skills": "text"}'),
('Emergency Transportation', 'Travel assistance for urgent medical or family needs', 'bus', 'Transport', 'active', '["validid"]', '{"destination": "text", "purpose": "text"}');

-- Administrators are intentionally not seeded. Provision the first account with
-- a unique random password through a protected operational process.

-- Verify data insertion
SELECT COUNT(*) as total_programs FROM programs;
SELECT COUNT(*) as total_users FROM users;

COMMIT;
