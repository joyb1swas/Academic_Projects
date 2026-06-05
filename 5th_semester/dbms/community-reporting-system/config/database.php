<?php
$host = 'localhost';
$dbname = 'community_reports';
$username = 'root';
$password = '';

try {
    // 1. Connect to MySQL server first without selecting a database
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Check if the database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE '$dbname'");
    $dbExists = $stmt->fetch();

    if (!$dbExists) {
        // Database doesn't exist, let's auto-create and seed it
        $schemaPath = __DIR__ . '/../sql/schema.sql';
        if (file_exists($schemaPath)) {
            $sql = file_get_contents($schemaPath);
            // Execute the schema which contains CREATE DATABASE, USE, and table definitions
            $pdo->exec($sql);
        } else {
            die("Database '$dbname' does not exist and sql/schema.sql was not found to auto-create it.");
        }
    }

    // 3. Connect specifically to the database for the application to use
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database connection/setup failed: " . $e->getMessage());
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
