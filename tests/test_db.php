<?php
require_once __DIR__ . '/../config/Database.php';

try {
    $pdo = Database::getInstance();
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    echo "✅ DB connected. Users count: " . $stmt->fetchColumn();
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}