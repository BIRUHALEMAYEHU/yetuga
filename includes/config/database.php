<?php
class Database {
    private $host = "localhost";
    private $db_name = "yetuga";
    private $username = "root";
    private $password = "";
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch(PDOException $e) {
            // Log the error to a file
            error_log("Database Connection Error: " . $e->getMessage());
            // Return null instead of echoing
            return null;
        }
    }
}
?> 