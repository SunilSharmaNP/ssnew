<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'flipmart');

// Site configuration
define('SITE_NAME', 'FlipMart');
define('SITE_URL', 'http://localhost:5000');

// Telegram Bot Configuration (to be configured later)
define('TELEGRAM_BOT_TOKEN', '');
define('TELEGRAM_CHANNEL_ID', '');

// Database connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // For now, just use a simple database creation script
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
        $pdo->exec("USE " . DB_NAME);
        // Include schema creation
        if (file_exists('private/schema.sql')) {
            $sql = file_get_contents('private/schema.sql');
            $pdo->exec($sql);
        }
    } catch(PDOException $e2) {
        die("Database connection failed: " . $e2->getMessage());
    }
}

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set cache control headers
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
?>