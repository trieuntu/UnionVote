-- UnionVote Database Schema
-- Hệ thống Bình chọn Online - Đoàn Khoa CNTT - ĐH Nha Trang

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS unionvote
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE unionvote;

-- Bảng users
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'editor') NOT NULL DEFAULT 'editor',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    last_login DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng elections
DROP TABLE IF EXISTS elections;
CREATE TABLE elections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    min_votes INT NOT NULL DEFAULT 1 COMMENT 'Số lượng tối thiểu phải chọn',
    max_votes INT NOT NULL DEFAULT 1 COMMENT 'Số lượng tối đa được chọn',
    is_visible TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Hiển thị trên trang chủ',
    show_result TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Hiển thị kết quả realtime',
    status ENUM('draft', 'active', 'completed', 'cancelled') NOT NULL DEFAULT 'draft',
    created_by INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng candidates
DROP TABLE IF EXISTS candidates;
CREATE TABLE candidates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    election_id INT NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    class_name VARCHAR(50) NOT NULL,
    student_id VARCHAR(20) NOT NULL,
    gpa DECIMAL(3,2) NULL COMMENT 'Điểm TB tích luỹ',
    conduct_score DECIMAL(5,2) NULL COMMENT 'Điểm rèn luyện tích luỹ',
    bio TEXT NULL COMMENT 'Tóm tắt thông tin cá nhân',
    display_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (election_id) REFERENCES elections(id) ON DELETE CASCADE,
    UNIQUE KEY unique_candidate (election_id, student_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng voters
DROP TABLE IF EXISTS voters;
CREATE TABLE voters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    election_id INT NOT NULL,
    email VARCHAR(100) NOT NULL,
    has_voted TINYINT(1) NOT NULL DEFAULT 0,
    voted_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (election_id) REFERENCES elections(id) ON DELETE CASCADE,
    UNIQUE KEY unique_voter (election_id, email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng email_tokens
DROP TABLE IF EXISTS email_tokens;
CREATE TABLE email_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    election_id INT NOT NULL,
    voter_id INT NOT NULL,
    token_hash VARCHAR(64) NOT NULL COMMENT 'SHA-256 hash của token',
    expires_at DATETIME NOT NULL,
    is_used TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (election_id) REFERENCES elections(id) ON DELETE CASCADE,
    FOREIGN KEY (voter_id) REFERENCES voters(id) ON DELETE CASCADE,
    INDEX idx_token_hash (token_hash),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng votes (ẩn danh - KHÔNG có FK tới voters)
DROP TABLE IF EXISTS votes;
CREATE TABLE votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    election_id INT NOT NULL,
    token_hash VARCHAR(64) NOT NULL COMMENT 'Hash token đã sử dụng để bỏ phiếu',
    ip_hash VARCHAR(64) NULL COMMENT 'Hash IP người bỏ phiếu',
    submitted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (election_id) REFERENCES elections(id) ON DELETE CASCADE,
    UNIQUE KEY unique_token_vote (token_hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng vote_details
DROP TABLE IF EXISTS vote_details;
CREATE TABLE vote_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vote_id INT NOT NULL,
    candidate_id INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vote_id) REFERENCES votes(id) ON DELETE CASCADE,
    FOREIGN KEY (candidate_id) REFERENCES candidates(id) ON DELETE CASCADE,
    UNIQUE KEY unique_vote_candidate (vote_id, candidate_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng settings
DROP TABLE IF EXISTS settings;
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
