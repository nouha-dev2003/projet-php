<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/Database.php';

use Config\Database;

try {
    $pdo = Database::getInstance();
    
    $email = 'admin@example.com';
    $plainPassword = 'admin123';
    
    // Generate a brand new hash from PHP
    $newHash = password_hash($plainPassword, PASSWORD_DEFAULT);
    
    // Update the database
    $sql = "UPDATE users SET password_hash = :hash WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':hash' => $newHash, ':email' => $email]);
    
    if ($stmt->rowCount() > 0) {
        echo "✅ Password hash updated successfully for $email<br>";
        echo "New hash: $newHash<br>";
        echo "Now try logging in again with password: <strong>admin123</strong>";
    } else {
        echo "❌ User with email $email not found. Please run the INSERT first.";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}