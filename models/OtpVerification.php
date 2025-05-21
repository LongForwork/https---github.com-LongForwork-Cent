<?php
require_once __DIR__. '/../config/database.php';
require_once 'config/config.php';

/**
 * Model xử lý dữ liệu xác thực OTP
 */
class OtpVerification {
    private $conn;
    private $table_name = "otp_verifications";

    /**
     * Khởi tạo kết nối đến cơ sở dữ liệu
     */
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Tạo mã OTP mới
     * 
     * @param string $email Email nhận mã OTP
     * @param string $otp_code Mã OTP
     * @return bool Trạng thái tạo mã OTP
     */
    public function create($email, $otp_code) {
        // Xóa mã OTP cũ nếu có
        $this->deleteByEmail($email);
        
        // Tạo mã OTP mới
        $query = "INSERT INTO " . $this->table_name . " 
                  (email, otp_code, expires_at, created_at) 
                  VALUES (?, ?, DATE_ADD(NOW(), INTERVAL " . OTP_EXPIRY_MINUTES . " MINUTE), NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([$email, $otp_code]);
    }

    /**
     * Xác thực mã OTP
     * 
     * @param string $email Email nhận mã OTP
     * @param string $otp_code Mã OTP cần xác thực
     * @return bool Trạng thái xác thực
     */
    public function verify($email, $otp_code) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE email = ? AND otp_code = ? AND expires_at > NOW()";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email, $otp_code]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Xóa mã OTP theo email
     * 
     * @param string $email Email cần xóa mã OTP
     * @return bool Trạng thái xóa
     */
    public function deleteByEmail($email) {
        $query = "DELETE FROM " . $this->table_name . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([$email]);
    }

    /**
     * Kiểm tra số lần gửi OTP trong khoảng thời gian
     * 
     * @param string $email Email cần kiểm tra
     * @param int $minutes Khoảng thời gian (phút)
     * @return int Số lần gửi OTP
     */
    public function countRecentAttempts($email, $minutes = 60) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                  WHERE email = ? AND created_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email, $minutes]);
        $result = $stmt->fetch();

        return (int)$result['count'];
    }
}
