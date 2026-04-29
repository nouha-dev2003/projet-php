<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/daos/UserDao.php';
require_once __DIR__ . '/../models/entities/User.php';

use Config\Database;

$pdo = Database::getInstance();
$userDao = new UserDao($pdo);
$user = $userDao->findByEmail('admin@example.com');

if (!$user) {
    die("❌ User not found. Run INSERT first.");
}

$storedHash = $user->getPasswordHash();
echo "Stored hash: $storedHash<br>";

// Test with hardcoded password
$testPassword = 'admin123';
if (password_verify($testPassword, $storedHash)) {
    echo "✅ Hardcoded 'admin123' matches.<br>";
} else {
    echo "❌ Hardcoded 'admin123' does NOT match – hash may be corrupted.<br>";
}

// Now test with actual POST data if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedPassword = $_POST['password'] ?? '';
    echo "<hr>Password from POST:<br>";
    echo "Raw: [" . $postedPassword . "]<br>";
    echo "Length: " . strlen($postedPassword) . "<br>";
    echo "Hex: " . bin2hex($postedPassword) . "<br>";
    
    if (password_verify($postedPassword, $storedHash)) {
        echo "✅ POST password matches!<br>";
    } else {
        echo "❌ POST password does NOT match.<br>";
        // Try trimming
        $trimmed = trim($postedPassword);
        if ($trimmed !== $postedPassword && password_verify($trimmed, $storedHash)) {
            echo "✅ But trimmed version matches! (Password has leading/trailing whitespace)<br>";
        }
    }
} else {
    echo '<hr><form method="POST">';
    echo '<input type="password" name="password" placeholder="Enter password">';
    echo '<button type="submit">Test</button>';
    echo '</form>';
}
?>