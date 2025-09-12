<?php
// Database setup script
$host = '';
$username = '';
$password = '';

try {
    // Connect to MySQL server
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS customer_db");
    echo "Database 'customer_db' created successfully.<br>";
    
    // Select the database
    $pdo->exec("USE customer_db");
    
    // Create customers table
    $sql = "CREATE TABLE IF NOT EXISTS customers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        lastname VARCHAR(255) NOT NULL,
        firstname VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        city VARCHAR(255) NOT NULL,
        country VARCHAR(255) NOT NULL,
        image_path VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "Table 'customers' created successfully.<br>";
    
    echo "<br>Database setup completed! <a href='index.php'>Go to main page</a>";
    
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
