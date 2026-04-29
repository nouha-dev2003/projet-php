<?php
// utils/Auth.php

class Auth
{
    private static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', '1');
            ini_set('session.use_only_cookies', '1');
            ini_set('session.cookie_samesite', 'Strict');
            session_start();
        }
    }

    public static function isLoggedIn(): bool
    {
        self::startSession();
        return isset($_SESSION['user_id']);
    }

    /**
     * Redirect to login page if not authenticated.
     * Optionally saves the current URL to redirect back after login.
     *
     * @param string|null $redirectUrl URL to return to after login
     */
    public static function requireLogin(?string $redirectUrl = null): void
    {
        if (!self::isLoggedIn()) {
            if ($redirectUrl) {
                $_SESSION['redirect_after_login'] = $redirectUrl;
            }
            header('Location: index.php?route=auth/login');
            exit();
        }
    }

    /**
     * Log in a user – stores session data and regenerates session ID.
     *
     * @param User $user
     */
    public static function login(User $user): void
    {
        self::startSession();
        session_regenerate_id(true);
        $_SESSION['user_id']    = $user->getId();
        $_SESSION['user_email'] = $user->getEmail();
        $_SESSION['user_role']  = $user->getRole();
    }

    public static function logout(): void
    {
        self::startSession();
        $_SESSION = [];
        session_destroy();

        // Delete session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
    }

    public static function getUserId(): ?int
    {
        return self::isLoggedIn() ? $_SESSION['user_id'] : null;
    }

    public static function getUserRole(): ?string
    {
        return self::isLoggedIn() ? $_SESSION['user_role'] : null;
    }

    public static function isAdmin(): bool
    {
        return self::isLoggedIn() && $_SESSION['user_role'] === 'admin';
    }
}