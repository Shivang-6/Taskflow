<?php
require_once __DIR__ . "/../Libraries/JWT.php";
require_once __DIR__ . "/../Models/TaskModel.php";

class TaskController {
    private $taskModel;
    private $jwt;
    
    public function __construct() {
        $this->taskModel = new TaskModel();
        $this->jwt = new JWT();
    }
    
    private function authenticate() {
        $headers = getallheaders();
        if (!isset($headers["Authorization"])) {
            return false;
        }
        
        $authHeader = $headers["Authorization"];
        $token = str_replace("Bearer ", "", $authHeader);
        
        return $this->jwt->decode($token);
    }
    
    public function getTasks() {
        $user = $this->authenticate();
        if (!$user) {
            http_response_code(401);
            return ["status" => "error", "message" => "Unauthorized"];
        }
        
        $tasks = $this->taskModel->getUserTasks($user["user_id"]);
        return ["status" => "success", "data" => $tasks];
    }
}
?>
