<?php

require_once __DIR__ . '/../includes/security.php';
start_secure_session();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token()) {
    $_SESSION['login_error'] = 'Invalid request. Please try again.';
    header('Location: ../index.php');
    exit();
}

if (isset($_POST['register'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = 'user';

    foreach ([validate_name($name), validate_email($email), validate_password($password)] as $error) {
        if ($error !== null) {
            $_SESSION['register_error'] = $error;
            $_SESSION['active_form'] = 'register-form';
            header('Location: ../index.php');
            exit();
        }
    }

    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['register_error'] = 'Email already registered';
        $_SESSION['active_form'] = 'register-form';
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $name, $email, $hashedPassword, $role);
        $stmt->execute();
    }

    header('Location: ../index.php');
    exit();
}

if (isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $rateLimitError = check_login_rate_limit();
    if ($rateLimitError !== null) {
        $_SESSION['login_error'] = $rateLimitError;
        $_SESSION['active_form'] = 'login-form';
        header('Location: ../index.php');
        exit();
    }

    if (validate_email($email) !== null) {
        $_SESSION['login_error'] = 'Invalid email or password';
        $_SESSION['active_form'] = 'login-form';
        header('Location: ../index.php');
        exit();
    }

    $stmt = $conn->prepare('SELECT id, name, email, password, role FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        clear_login_failures();

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header('Location: ../pages/admin.php');
        } else {
            header('Location: ../pages/user.php');
        }
        exit();
    }

    record_login_failure();
    $_SESSION['login_error'] = 'Invalid email or password';
    $_SESSION['active_form'] = 'login-form';
    header('Location: ../index.php');
    exit();
}

header('Location: ../index.php');
exit();
