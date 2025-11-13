<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in(): bool {
    return !empty($_SESSION['admin_logged']);
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: /EPORT/CHRISYSTEMATIXX/admin/login.php');
        exit;
    }
}

function login(string $password): bool {
    // Change this password after first login
    $ADMIN_PASSWORD = 'admin123';
    if (hash_equals($ADMIN_PASSWORD, $password)) {
        $_SESSION['admin_logged'] = true;
        regenerate_csrf();
        return true;
    }
    return false;
}

function logout(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'], $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf_token'];
}

function regenerate_csrf(): void {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

function verify_csrf(?string $token): bool {
    return is_string($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
