-- Tạo cơ sở dữ liệu
CREATE DATABASE IF NOT EXISTS cent_beauty CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cent_beauty;

-- Bảng chi nhánh
CREATE TABLE IF NOT EXISTS branches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Bảng dịch vụ
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    duration INT NOT NULL COMMENT 'Thời gian dịch vụ tính bằng phút',
    branch_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Bảng người dùng
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Bảng đặt lịch
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    branch_id INT NOT NULL,
    service_id INT NOT NULL,
    date_time DATETIME NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Bảng xác thực OTP
CREATE TABLE IF NOT EXISTS otp_verifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    otp_code VARCHAR(6) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (email)
) ENGINE=InnoDB;

-- Thêm dữ liệu mẫu cho chi nhánh
INSERT INTO branches (name, address) VALUES
('Cầu Giấy', 'Số 8 ngõ 10 Nguyễn Văn Huyên, Quận Cầu Giấy, Hà Nội'),
('Hai Bà Trưng', 'Số 20 LÔ TT2A Ngõ 622 Minh Khai, Quận Hai Bà Trưng, Hà Nội'),
('Đống Đa', 'Số 35-37 Trần Hữu Tước, Quận Đống Đa, Hà Nội'),
('Ba Đình', 'Số 97 Ngõ Núi Trúc, Quận Ba Đình, Hà Nội');

-- Thêm dữ liệu mẫu cho dịch vụ
INSERT INTO services (name, description, price, duration, branch_id) VALUES
('Cắt tóc nữ', 'Cắt tóc nữ theo yêu cầu', 150000, 45, 1),
('Cắt tóc nam', 'Cắt tóc nam theo yêu cầu', 100000, 30, 1),
('Nhuộm tóc', 'Nhuộm tóc màu thời trang', 500000, 120, 1),
('Uốn tóc', 'Uốn tóc theo yêu cầu', 600000, 150, 1),
('Gội đầu dưỡng sinh', 'Gội đầu kết hợp massage thư giãn', 200000, 60, 1),

('Cắt tóc nữ', 'Cắt tóc nữ theo yêu cầu', 150000, 45, 2),
('Cắt tóc nam', 'Cắt tóc nam theo yêu cầu', 100000, 30, 2),
('Nhuộm tóc', 'Nhuộm tóc màu thời trang', 500000, 120, 2),
('Uốn tóc', 'Uốn tóc theo yêu cầu', 600000, 150, 2),
('Gội đầu dưỡng sinh', 'Gội đầu kết hợp massage thư giãn', 200000, 60, 2),

('Cắt tóc nữ', 'Cắt tóc nữ theo yêu cầu', 150000, 45, 3),
('Cắt tóc nam', 'Cắt tóc nam theo yêu cầu', 100000, 30, 3),
('Nhuộm tóc', 'Nhuộm tóc màu thời trang', 500000, 120, 3),
('Uốn tóc', 'Uốn tóc theo yêu cầu', 600000, 150, 3),
('Gội đầu dưỡng sinh', 'Gội đầu kết hợp massage thư giãn', 200000, 60, 3),

('Cắt tóc nữ', 'Cắt tóc nữ theo yêu cầu', 150000, 45, 4),
('Cắt tóc nam', 'Cắt tóc nam theo yêu cầu', 100000, 30, 4),
('Nhuộm tóc', 'Nhuộm tóc màu thời trang', 500000, 120, 4),
('Uốn tóc', 'Uốn tóc theo yêu cầu', 600000, 150, 4),
('Gội đầu dưỡng sinh', 'Gội đầu kết hợp massage thư giãn', 200000, 60, 4);
