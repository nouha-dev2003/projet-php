<?php
// controllers/AuthController.php
use Config\Database;
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/daos/UserDao.php';
require_once __DIR__ . '/../models/entities/User.php';
require_once __DIR__ . '/../utils/Auth.php';

class AuthController
{
    private UserDao $userDao;

    public function __construct()
    {
        $pdo = Database::getInstance();
        $this->userDao = new UserDao($pdo);
    }

    /**
     * Show the login form.
     */
    public function login(): void
    {
        if (Auth::isLoggedIn()) {
            header('Location: index.php?route=auth/dashboard');
            exit();
        }
        require __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Process login form submission.
     */
    public function authenticate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?route=auth/login');
            exit();
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $_SESSION['login_error'] = 'Email and password are required.';
            header('Location: index.php?route=auth/login');
            exit();
        }

        $user = $this->userDao->findByEmail($email);

        if (!$user) {
            $_SESSION['login_error'] = 'Invalid email or password.';
            header('Location: index.php?route=auth/login');
            exit();
        }

        if (password_verify($password, $user->getPasswordHash())) {
            Auth::login($user);
            $redirect = $_SESSION['redirect_after_login'] ?? 'index.php?route=auth/dashboard';
            unset($_SESSION['redirect_after_login']);
            header('Location: ' . $redirect);
            exit();
        } else {
            $_SESSION['login_error'] = 'Invalid email or password.';
            header('Location: index.php?route=auth/login');
            exit();
        }
    }
    /**
     * Dashboard (protected page).
     */
    public function dashboard(): void
    {
        Auth::requireLogin();
        $userEmail = $_SESSION['user_email'] ?? 'User';
        require __DIR__ . '/../views/auth/dashboard.php';
    }

    /**
     * Logout.
     */
    public function logout(): void
    {
        Auth::logout();
        header('Location: index.php?route=auth/login');
        exit();
    }
}