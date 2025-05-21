<?php
require_once __DIR__. '/../config/database.php';

/**
 * Model xử lý dữ liệu dịch vụ
 */
class Service {
    private $conn;
    private $table_name = "services";

    /**
     * Khởi tạo kết nối đến cơ sở dữ liệu
     */
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Lấy danh sách dịch vụ theo chi nhánh
     * 
     * @param int $branch_id ID chi nhánh
     * @return array Danh sách dịch vụ
     */
    public function getServicesByBranchId($branch_id) {
        $query = "SELECT id, name, description, price, duration FROM " . $this->table_name . " 
                  WHERE branch_id = ? ORDER BY id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$branch_id]);

        return $stmt->fetchAll();
    }

    /**
     * Lấy thông tin dịch vụ theo ID
     * 
     * @param int $id ID dịch vụ
     * @return array|false Thông tin dịch vụ hoặc false nếu không tìm thấy
     */
    public function getServiceById($id) {
        $query = "SELECT id, name, description, price, duration, branch_id FROM " . $this->table_name . " 
                  WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);

        return $stmt->fetch();
    }
}
