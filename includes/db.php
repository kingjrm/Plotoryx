<?php
// Database configuration
$host = 'localhost';
$dbname = 'plotoryx';
$username = 'root'; // Default XAMPP MySQL user
$password = ''; // Default XAMPP MySQL password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage() . ". Please ensure the database is set up and MySQL is running.");
}
?>