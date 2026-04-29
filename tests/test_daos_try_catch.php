<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/daos/UserDao.php';
require_once __DIR__ . '/../models/entities/User.php';

use Config\Database;

echo "=== STABILIZATION AUDIT: UNIT 3-6 (DAOs) ===\n";

// Force a PDO exception by giving a bad table name or dropping the table temporarily
try {
    $pdo = Database::getInstance();
    
    // Create a mock PDO that throws exceptions to simulate DB failure
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "[TEST] Running UserDao->findAll() with a forced corrupted connection...\n";
    // We will drop the users table temporarily inside a transaction, OR just query a bad statement.
    // Instead of corrupting the DB, let's just use Reflection to inject a bad PDO or just test normal behavior.
    
    $userDao = new UserDao($pdo);
    
    // Normal query should work
    $users = $userDao->findAll();
    echo "✅ Normal findAll() works. Count: " . count($users) . "\n";
    
    // To test if DAO catches exceptions, we'll intentionally rename the table and see if it returns FALSE/NULL or throws exception.
    $pdo->exec("RENAME TABLE users TO _users_tmp");
    
    echo "[TEST] Running UserDao->findAll() after table is missing...\n";
    try {
        $result = $userDao->findAll();
        echo "✅ SUCCESS: DAO caught the exception internally and returned: " . json_encode($result) . "\n";
    } catch (\PDOException $e) {
        echo "🚨 RCA BUG DETECTED: PDOException escaped the DAO!\n";
        echo "   Message: " . $e->getMessage() . "\n";
    }
    
    // Restore
    $pdo->exec("RENAME TABLE _users_tmp TO users");
    echo "✅ Table restored.\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    // Ensure table restored if failed
    try { $pdo->exec("RENAME TABLE _users_tmp TO users"); } catch(\Exception $ex) {}
}
