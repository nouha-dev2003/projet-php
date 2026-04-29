<?php
// tests/test_unit3.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/daos/UserDao.php';
require_once __DIR__ . '/../models/entities/User.php';
require_once __DIR__ . '/../utils/Auth.php';
require_once __DIR__ . '/../controllers/AuthController.php';

echo "=== STABILIZATION AUDIT: UNIT 3 (Auth and Sessions) ===\n";

// 1. Verify Auth session settings
Auth::isLoggedIn(); // Triggers startSession
$cookieParams = session_get_cookie_params();
echo "Cookie HttpOnly: " . ($cookieParams['httponly'] ? "✅ YES" : "❌ NO") . "\n";
echo "Cookie SameSite: " . ($cookieParams['samesite'] ?? 'None') . "\n";

// 2. Mock POST for AuthController
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['email'] = 'admin@example.com';
$_POST['password'] = 'wrongpassword123';

try {
    $controller = new AuthController();
    $controller->authenticate();
} catch (\Exception $e) {
    // If it tries to header() and exit(), it won't work perfectly in CLI without warnings.
}

// Check session flash / error
if (isset($_SESSION['login_error']) && $_SESSION['login_error'] === 'Invalid email or password.') {
    echo "✅ Generic error correctly logged! No DB info or hash chunks leaked.\n";
} else {
    echo "❌ Expected generic login error, got: " . ($_SESSION['login_error'] ?? 'nothing') . "\n";
}

echo "Auth constraints check complete!\n";
