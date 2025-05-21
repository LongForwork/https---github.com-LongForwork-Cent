<?php
require_once __DIR__. '/../config/database.php';

/**
 * Model xử lý dữ liệu chi nhánh
 */
class Branch {
    private $conn;
    private $table_name = "branches";

    /**
     * Khởi tạo kết nối đến cơ sở dữ liệu
     */
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Lấy tất cả chi nhánh
     * 
     * @return array Danh sách chi nhánh
     */
    public function getAllBranches() {
        $query = "SELECT id, name, address FROM " . $this->table_name . " ORDER BY id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Lấy thông tin chi nhánh theo ID
     * 
     * @param int $id ID chi nhánh
     * @return array|false Thông tin chi nhánh hoặc false nếu không tìm thấy
     */
    public function getBranchById($id) {
        $query = "SELECT id, name, address FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);

        return $stmt->fetch();
    }
}
