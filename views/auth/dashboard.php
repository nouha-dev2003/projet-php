<?php
// Ensure Auth utility is available if not already included by the router
require_once __DIR__ . '/../../utils/Auth.php';

// Get user info from session safely
$userEmail = $_SESSION['user_email'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Antigravity</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header class="flex justify-between items-center mb-4">
            <div>
                <h1>Welcome, <?= htmlspecialchars($userEmail) ?>!</h1>
                <p style="color: var(--text-muted);"><strong>Role:</strong> <span style="color: var(--primary);"><?= htmlspecialchars(Auth::getUserRole() ?? 'User') ?></span></p>
            </div>
            <div class="actions">
                <a href="index.php?route=auth/logout" class="btn btn-danger">Logout</a>
            </div>
        </header>

        <hr>

        <main>
            <h3 class="mb-4">Quick Navigation</h3>
            <nav>
                <ul class="flex gap-4">
                    <li>
                        <a href="index.php?route=products/index" class="btn">Manage Products</a>
                    </li>
                    
                    <?php if (Auth::isAdmin()): ?>
                        <li>
                            <a href="index.php?route=filemanager/index" class="btn">File Manager</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </main>
    </div>
</body>
</html>