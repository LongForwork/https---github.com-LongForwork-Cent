-- Cập nhật bảng users
ALTER TABLE users 
CHANGE COLUMN phone_number email VARCHAR(100) NOT NULL;

-- Cập nhật bảng otp_verifications
ALTER TABLE otp_verifications
CHANGE COLUMN phone_number email VARCHAR(100) NOT NULL;
