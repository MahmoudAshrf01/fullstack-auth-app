<?php

require_once __DIR__ . '/../includes/security.php';
start_secure_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['logout']) || !verify_csrf_token()) {
    header('Location: ../index.php');
    exit();
}

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

header('Location: ../index.php');
exit();
