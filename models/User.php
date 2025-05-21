<?php
require_once __DIR__. '/../config/database.php';

/**
 * Model xử lý dữ liệu người dùng
 */
class User {
    private $conn;
    private $table_name = "users";

    /**
     * Khởi tạo kết nối đến cơ sở dữ liệu
     */
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Tìm người dùng theo email
     * 
     * @param string $email Email cần tìm
     * @return array|false Thông tin người dùng hoặc false nếu không tìm thấy
     */
    public function findByEmail($email) {
        $query = "SELECT id, email, name FROM " . $this->table_name . " 
                  WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);

        return $stmt->fetch();
    }

    /**
     * Tạo người dùng mới
     * 
     * @param string $email Email người dùng
     * @param string|null $name Tên người dùng (không bắt buộc)
     * @return int|false ID người dùng mới hoặc false nếu thất bại
     */
    public function create($email, $name = null) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (email, name, created_at) 
                  VALUES (?, ?, NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        if($stmt->execute([$email, $name])) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }

    /**
     * Cập nhật thông tin người dùng
     * 
     * @param int $id ID người dùng
     * @param array $data Dữ liệu cần cập nhật
     * @return bool Trạng thái cập nhật
     */
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        
        $values[] = $id;
        
        $query = "UPDATE " . $this->table_name . " 
                  SET " . implode(", ", $fields) . ", updated_at = NOW() 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute($values);
    }
}
