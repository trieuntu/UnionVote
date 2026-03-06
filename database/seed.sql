-- UnionVote Seed Data
USE unionvote;

-- Admin mặc định (password: Admin@123)
INSERT INTO users (username, email, password_hash, full_name, role) VALUES
('admin', 'admin@ntu.edu.vn', '$2y$12$qjW3IN4Aks6y9Jjx6aOhrOwUMmguBtDD25.CwZUBsPS6NZc/8BssC', 'Quản trị viên', 'admin');

-- Cấu hình SMTP mặc định
INSERT INTO settings (setting_key, setting_value) VALUES
('smtp_host', ''),
('smtp_port', '587'),
('smtp_username', ''),
('smtp_password', ''),
('smtp_encryption', 'tls'),
('smtp_from_email', ''),
('smtp_from_name', 'UnionVote - ĐK CNTT NTU'),
('site_name', 'Hệ thống Bình chọn Online - Đoàn Khoa CNTT'),
('token_expiry_minutes', '15');
