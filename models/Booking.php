<?php
require_once __DIR__. '/../config/database.php';

/**
 * Model xử lý dữ liệu đặt lịch
 */
class Booking {
    private $conn;
    private $table_name = "bookings";

    /**
     * Khởi tạo kết nối đến cơ sở dữ liệu
     */
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Tạo đặt lịch mới
     * 
     * @param int $user_id ID người dùng
     * @param int $branch_id ID chi nhánh
     * @param int $service_id ID dịch vụ
     * @param string $date_time Thời gian đặt lịch (format: Y-m-d H:i:s)
     * @param string|null $notes Ghi chú (không bắt buộc)
     * @return int|false ID đặt lịch mới hoặc false nếu thất bại
     */
    public function create($user_id, $branch_id, $service_id, $date_time, $notes = null) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (user_id, branch_id, service_id, date_time, status, notes, created_at) 
                  VALUES (?, ?, ?, ?, 'pending', ?, NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        if($stmt->execute([$user_id, $branch_id, $service_id, $date_time, $notes])) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }

    /**
     * Lấy thông tin đặt lịch theo ID
     * 
     * @param int $id ID đặt lịch
     * @return array|false Thông tin đặt lịch hoặc false nếu không tìm thấy
     */
    public function getBookingById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);

        return $stmt->fetch();
    }

    /**
     * Lấy danh sách đặt lịch theo người dùng
     * 
     * @param int $user_id ID người dùng
     * @return array Danh sách đặt lịch
     */
    public function getBookingsByUserId($user_id) {
        $query = "SELECT b.id, b.date_time, b.status, b.notes, 
                         br.name as branch_name, br.address as branch_address,
                         s.name as service_name, s.price as service_price, s.duration as service_duration
                  FROM " . $this->table_name . " b
                  JOIN branches br ON b.branch_id = br.id
                  JOIN services s ON b.service_id = s.id
                  WHERE b.user_id = ?
                  ORDER BY b.date_time DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);

        return $stmt->fetchAll();
    }

    /**
     * Cập nhật trạng thái đặt lịch
     * 
     * @param int $id ID đặt lịch
     * @param string $status Trạng thái mới
     * @return bool Trạng thái cập nhật
     */
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = ?, updated_at = NOW() 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([$status, $id]);
    }
}
