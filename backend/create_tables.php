<?php
require_once "app/Config/Database.php";
require_once "app/Models/UserModel.php";
require_once "app/Models/TaskModel.php";

echo "Creating database tables...\n";

// Just creating instances will create tables
try {
    $userModel = new UserModel();
    echo "✓ Users table created/checked\n";
    
    $taskModel = new TaskModel();
    echo "✓ Tasks table created/checked\n";
    
    echo "\n✅ Tables created successfully!\n";
    echo "Database is ready for use.\n";
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Please check MySQL is running and credentials are correct.\n";
}
?>
