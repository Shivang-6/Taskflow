<?php
// Simple API Router
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    exit(0);
}

$request_uri = $_SERVER["REQUEST_URI"];
$request_method = $_SERVER["REQUEST_METHOD"];

// Remove query string
$request_uri = strtok($request_uri, "?");

// Simple routing
switch (true) {
    case $request_method === "POST" && strpos($request_uri, "/api/register") !== false:
        require_once __DIR__ . "/../app/Controllers/AuthController.php";
        $data = json_decode(file_get_contents("php://input"), true);
        $controller = new AuthController();
        echo json_encode($controller->register($data));
        break;
        
    case $request_method === "POST" && strpos($request_uri, "/api/login") !== false:
        require_once __DIR__ . "/../app/Controllers/AuthController.php";
        $data = json_decode(file_get_contents("php://input"), true);
        $controller = new AuthController();
        echo json_encode($controller->login($data));
        break;
        
    case $request_method === "GET" && strpos($request_uri, "/api/tasks") !== false:
        require_once __DIR__ . "/../app/Controllers/TaskController.php";
        $controller = new TaskController();
        echo json_encode($controller->getTasks());
        break;
        
    default:
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Endpoint not found"]);
}
?>
