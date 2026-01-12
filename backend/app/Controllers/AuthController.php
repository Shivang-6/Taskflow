<?php
require_once __DIR__ . "/../Libraries/JWT.php";
require_once __DIR__ . "/../Models/UserModel.php";

class AuthController {
    private $userModel;
    private $jwt;
    
    public function __construct() {
        $this->userModel = new UserModel();
        $this->jwt = new JWT();
    }
    
    public function register($data) {
        if (empty($data["email"]) || empty($data["password"]) || empty($data["full_name"])) {
            return ["status" => "error", "message" => "All fields are required"];
        }
        
        if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
            return ["status" => "error", "message" => "Invalid email format"];
        }
        
        if (strlen($data["password"]) < 6) {
            return ["status" => "error", "message" => "Password must be at least 6 characters"];
        }
        
        $result = $this->userModel->createUser($data);
        
        if ($result["success"]) {
            $token = $this->jwt->encode(["user_id" => $result["user_id"], "email" => $data["email"]]);
            return [
                "status" => "success",
                "message" => "Registration successful",
                "data" => [
                    "user" => [
                        "id" => $result["user_id"],
                        "email" => $data["email"],
                        "full_name" => $data["full_name"]
                    ],
                    "token" => $token
                ]
            ];
        } else {
            return ["status" => "error", "message" => $result["message"]];
        }
    }
    
    public function login($data) {
        if (empty($data["email"]) || empty($data["password"])) {
            return ["status" => "error", "message" => "Email and password are required"];
        }
        
        $user = $this->userModel->getUserByEmail($data["email"]);
        
        if (!$user || !password_verify($data["password"], $user["password_hash"])) {
            return ["status" => "error", "message" => "Invalid credentials"];
        }
        
        $token = $this->jwt->encode(["user_id" => $user["id"], "email" => $user["email"]]);
        
        return [
            "status" => "success",
            "message" => "Login successful",
            "data" => [
                "user" => [
                    "id" => $user["id"],
                    "email" => $user["email"],
                    "full_name" => $user["full_name"]
                ],
                "token" => $token
            ]
        ];
    }
}
?>
