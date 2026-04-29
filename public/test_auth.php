<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/daos/UserDao.php';
require_once __DIR__ . '/../models/entities/User.php';

use Config\Database;

try {
    $pdo = Database::getInstance();
    $userDao = new UserDao($pdo);

    $email = 'admin@example.com';
    $user = $userDao->findByEmail($email);

    if (!$user) {
        die("❌ User NOT found in database. Please run the INSERT SQL.");
    }

    echo "✅ User found: " . $user->getEmail() . "<br>";
    echo "Stored hash: " . $user->getPasswordHash() . "<br><br>";

    $testPassword = 'admin123';
    if (password_verify($testPassword, $user->getPasswordHash())) {
        echo "✅ Password 'admin123' matches the hash.<br>";
        echo "Login should work now.";
    } else {
        echo "❌ Password does NOT match.<br>";
        echo "Re-run this exact SQL to fix the hash:<br>";
        echo "<pre>INSERT INTO users (email, password_hash, role, created_at) 
VALUES ('admin@example.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW())
ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), role = VALUES(role);</pre>";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}