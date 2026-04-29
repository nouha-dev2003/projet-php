<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Diagnostic Check</h2>";

// 1. Session
session_start();
$_SESSION['test'] = 'works';
echo "1. Session: " . ($_SESSION['test'] === 'works' ? "✅ OK" : "❌ Failed") . "<br>";

// 2. Database connection
require_once __DIR__ . '/../config/Database.php';
use Config\Database;
try {
    $pdo = Database::getInstance();
    echo "2. Database: ✅ Connected<br>";
} catch (Exception $e) {
    echo "2. Database: ❌ " . $e->getMessage() . "<br>";
    exit;
}

// 3. Check user
require_once __DIR__ . '/../models/daos/UserDao.php';
require_once __DIR__ . '/../models/entities/User.php';
$userDao = new UserDao($pdo);
$user = $userDao->findByEmail('admin@example.com');
if (!$user) {
    echo "3. User 'admin@example.com': ❌ Not found in database.<br>";
    echo "   Run this SQL:<br><pre>INSERT INTO users (email, password_hash, role, created_at) 
VALUES ('admin@example.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW());</pre>";
} else {
    echo "3. User found: ✅ " . $user->getEmail() . "<br>";
    echo "   Stored hash: " . $user->getPasswordHash() . "<br>";
    $testPassword = 'admin123';
    if (password_verify($testPassword, $user->getPasswordHash())) {
        echo "   Password verify: ✅ matches<br>";
    } else {
        echo "   Password verify: ❌ does NOT match<br>";
        echo "   Update hash with:<br><pre>UPDATE users SET password_hash = '"
             . password_hash($testPassword, PASSWORD_DEFAULT) . "' WHERE email = 'admin@example.com';</pre>";
    }
}

// 4. Router class
require_once __DIR__ . '/../config/Router.php';
echo "4. Router class: " . (class_exists('Router') ? "✅ Loaded" : "❌ Not found") . "<br>";