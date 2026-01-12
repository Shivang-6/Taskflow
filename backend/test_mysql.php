<?php
$passwords = ["", "root", "Shivang@123", "password", "admin"];

foreach ($passwords as $password) {
    try {
        $conn = new PDO("mysql:host=localhost", "root", $password);
        echo "✓ Success with password: \"" . $password . "\"\n";
        
        // Create database
        $conn->exec("CREATE DATABASE IF NOT EXISTS taskflow_db");
        echo "✓ Database created\n";
        
        // Show databases
        $stmt = $conn->query("SHOW DATABASES");
        $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "✓ Databases: " . implode(", ", $databases) . "\n";
        
        break;
    } catch (PDOException $e) {
        echo "✗ Failed with password: \"" . $password . "\" - " . $e->getMessage() . "\n";
    }
}
?>
