<?php

require_once __DIR__ . '/security.php';

function require_role(string $role): void
{
    start_secure_session();

    if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== $role) {
        header('Location: ../index.php');
        exit();
    }
}
