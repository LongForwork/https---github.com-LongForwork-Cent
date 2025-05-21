<?php
require_once 'config.php';

/**
 * Lớp kết nối và tương tác với cơ sở dữ liệu
 */
class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $conn;

    /**
     * Tạo kết nối đến cơ sở dữ liệu
     * 
     * @return PDO Đối tượng PDO kết nối đến cơ sở dữ liệu
     */
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch(PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            throw new Exception("Không thể kết nối đến cơ sở dữ liệu. Vui lòng thử lại sau.");
        }

        return $this->conn;
    }
}
