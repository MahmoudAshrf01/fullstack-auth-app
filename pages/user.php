<?php
require_once __DIR__ . '/../includes/auth_guard.php';
require_role('user');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <main class="auth-container">
        <div class="auth-card">
            <div class="auth-form active">
                <?php require_once __DIR__ . '/../includes/logout_button.php'; ?>
                <h1>User Dashboard</h1>
                <p class="auth-subtitle">Welcome, <?= e($_SESSION['name']) ?>!</p>
            </div>
        </div>
    </main>
</body>
</html>
