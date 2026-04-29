<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/entities/User.php';
require_once __DIR__ . '/../models/daos/UserDao.php';

use Config\Database;

try {
    $pdo = Database::getInstance();
    $userDao = new UserDao($pdo);  // No namespace – class is global
    $users = $userDao->findAll();

    echo "=== User List ===\n";
    if (empty($users)) {
        echo "No users found.\n";
    } else {
        echo "Found " . count($users) . " users:\n";
        foreach ($users as $user) {
            echo sprintf("ID: %d | Email: %s | Role: %s | Created: %s\n",
                $user->getId(),
                $user->getEmail(),
                $user->getRole(),
                $user->getCreatedAt()
            );
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}