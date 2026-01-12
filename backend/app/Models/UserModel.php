<?php
require_once __DIR__ . "/../Config/Database.php";

class UserModel {
    private $conn;
    private $table = "users";
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->createTableIfNotExists();
    }
    
    private function createTableIfNotExists() {
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            full_name VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        try {
            $this->conn->exec($sql);
        } catch (PDOException $e) {
            error_log("Error creating table: " . $e->getMessage());
        }
    }
    
    public function createUser($data) {
        $sql = "INSERT INTO users (email, password_hash, full_name) VALUES (:email, :password_hash, :full_name)";
        
        try {
            $stmt = $this->conn->prepare($sql);
            
            $hashed_password = password_hash($data["password"], PASSWORD_DEFAULT);
            
            $stmt->bindParam(":email", $data["email"]);
            $stmt->bindParam(":password_hash", $hashed_password);
            $stmt->bindParam(":full_name", $data["full_name"]);
            
            if ($stmt->execute()) {
                return ["success" => true, "user_id" => $this->conn->lastInsertId()];
            } else {
                return ["success" => false, "message" => "Failed to create user"];
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                return ["success" => false, "message" => "Email already exists"];
            }
            return ["success" => false, "message" => "Database error"];
        }
    }
    
    public function getUserByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting user: " . $e->getMessage());
            return false;
        }
    }
    
    public function getUserById($id) {
        $sql = "SELECT id, email, full_name, created_at FROM users WHERE id = :id LIMIT 1";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting user: " . $e->getMessage());
            return false;
        }
    }
}
?>
