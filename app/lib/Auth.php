<?php
class Auth {
    public static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login($username, $password) {
        if ($username === ADMIN_USER && $password === ADMIN_PASS) {
            self::startSession();
            $_SESSION['logged_in'] = true;
            $_SESSION['user'] = $username;
            $_SESSION['login_time'] = time();
            return true;
        }
        return false;
    }

    public static function isLoggedIn() {
        self::startSession();

        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
            return false;
        }

        // Check session timeout
        if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > SESSION_TIMEOUT) {
            self::logout();
            return false;
        }

        // Refresh login time
        $_SESSION['login_time'] = time();
        return true;
    }

    public static function logout() {
        self::startSession();
        session_destroy();
        return true;
    }

    public static function getCurrentUser() {
        self::startSession();
        return $_SESSION['user'] ?? null;
    }
}
?>
