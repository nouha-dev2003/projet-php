<?php
// views/auth/login.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Antigravity</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Welcome Back</h1>
        <p class="mb-4">Log in to manage your system.</p>

        <?php if (isset($_SESSION['login_error'])): ?>
            <div class="error"><?= htmlspecialchars($_SESSION['login_error']) ?></div>
            <?php unset($_SESSION['login_error']); ?>
        <?php endif; ?>

        <form method="POST" action="index.php?route=auth/authenticate">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required autofocus placeholder="Enter your email">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required placeholder="Enter your password">
            </div>
            <button type="submit" class="btn">Login Securely</button>
        </form>

        <p class="mt-4"><small style="color: var(--text-muted);">Demo: admin@example.com / admin123</small></p>
    </div>
</body>
</html>