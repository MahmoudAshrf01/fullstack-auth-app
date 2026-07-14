<?php

const MAX_LOGIN_ATTEMPTS = 5;
const LOGIN_LOCKOUT_SECONDS = 900;

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Strict',
    ]);

    session_start();
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf_token(): bool
{
    $token = $_POST['csrf_token'] ?? '';

    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function validate_name(string $name): ?string
{
    $name = trim($name);

    if ($name === '' || strlen($name) > 100) {
        return 'Name must be between 1 and 100 characters.';
    }

    if (!preg_match("/^[\p{L}\p{N}\s'\-\.]+$/u", $name)) {
        return 'Name contains invalid characters.';
    }

    return null;
}

function validate_email(string $email): ?string
{
    $email = trim($email);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Please enter a valid email address.';
    }

    if (strlen($email) > 255) {
        return 'Email is too long.';
    }

    return null;
}

function validate_password(string $password): ?string
{
    if (strlen($password) < 8) {
        return 'Password must be at least 8 characters.';
    }

    if (strlen($password) > 128) {
        return 'Password is too long.';
    }

    return null;
}

function login_rate_limit_key(): string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    return hash('sha256', $ip);
}

function login_rate_limit_path(string $key): string
{
    $dir = __DIR__ . '/../storage/rate_limit';

    if (!is_dir($dir)) {
        mkdir($dir, 0700, true);
    }

    return $dir . '/' . $key . '.json';
}

function check_login_rate_limit(): ?string
{
    $path = login_rate_limit_path(login_rate_limit_key());

    if (!file_exists($path)) {
        return null;
    }

    $data = json_decode(file_get_contents($path), true);

    if (!is_array($data) || empty($data['locked_until'])) {
        return null;
    }

    if ($data['locked_until'] > time()) {
        $minutes = (int) ceil(($data['locked_until'] - time()) / 60);

        return "Too many login attempts. Please try again in {$minutes} minute(s).";
    }

    unlink($path);

    return null;
}

function record_login_failure(): void
{
    $key = login_rate_limit_key();
    $path = login_rate_limit_path($key);
    $data = ['attempts' => 0, 'locked_until' => 0];

    if (file_exists($path)) {
        $existing = json_decode(file_get_contents($path), true);
        if (is_array($existing)) {
            $data = $existing;
        }
    }

    $data['attempts']++;

    if ($data['attempts'] >= MAX_LOGIN_ATTEMPTS) {
        $data['locked_until'] = time() + LOGIN_LOCKOUT_SECONDS;
    }

    file_put_contents($path, json_encode($data), LOCK_EX);
}

function clear_login_failures(): void
{
    $path = login_rate_limit_path(login_rate_limit_key());

    if (file_exists($path)) {
        unlink($path);
    }
}
