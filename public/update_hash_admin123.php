<?php
require_once __DIR__ . '/../config/Database.php';
use Config\Database;

$pdo = Database::getInstance();
$email = 'admin@example.com';
$newHash = password_hash('admin123', PASSWORD_DEFAULT);

$sql = "UPDATE users SET password_hash = :hash WHERE email = :email";
$stmt = $pdo->prepare($sql);
$stmt->execute([':hash' => $newHash, ':email' => $email]);

echo "Hash updated for $email to: " . $newHash;
echo "\n\nYou can now log in with password: admin123";