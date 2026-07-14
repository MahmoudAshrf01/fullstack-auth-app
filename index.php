<?php
require_once __DIR__ . '/includes/security.php';
start_secure_session();

$loginError = $_SESSION['login_error'] ?? null;
$registerError = $_SESSION['register_error'] ?? null;
$activeForm = $_SESSION['active_form'] ?? 'login-form';
if (!in_array($activeForm, ['login-form', 'register-form'], true)) {
    $activeForm = 'login-form';
}

unset($_SESSION['login_error'], $_SESSION['register_error'], $_SESSION['active_form']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auth App</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <main class="auth-container">
        <div class="auth-card">
            <!-- Login Form -->
            <form class="auth-form<?= $activeForm === 'login-form' ? ' active' : '' ?>" id="login-form" method="post" action="auth/login_register.php"<?= $activeForm !== 'login-form' ? ' hidden' : '' ?>>
                <?= csrf_field() ?>
                <h1>Login</h1>
                <p class="auth-subtitle">Welcome back! Please sign in.</p>

                <?php if ($loginError): ?>
                    <p class="auth-alert"><?= e($loginError) ?></p>
                <?php endif; ?>

                <div class="field-group">
                    <label for="login-email">Email</label>
                    <input id="login-email" name="email" type="email" placeholder="Enter your email"
                        autocomplete="email" required>
                </div>

                <div class="field-group">
                    <label for="login-password">Password</label>
                    <input id="login-password" name="password" type="password" placeholder="Enter your password"
                        autocomplete="current-password" required>
                </div>

                <button type="submit" name="login" class="btn-primary">Login</button>

                <p class="auth-switch">
                    Don't have an account?
                    <button type="button" class="link-btn" data-show="register-form">Register</button>
                </p>
            </form>

            <!-- Register Form -->
            <form class="auth-form<?= $activeForm === 'register-form' ? ' active' : '' ?>" id="register-form" method="post" action="auth/login_register.php"<?= $activeForm !== 'register-form' ? ' hidden' : '' ?>>
                <?= csrf_field() ?>
                <h1>Register</h1>
                <p class="auth-subtitle">Create your account to get started.</p>

                <?php if ($registerError): ?>
                    <p class="auth-alert"><?= e($registerError) ?></p>
                <?php endif; ?>

                <div class="field-group">
                    <label for="register-name">Name</label>
                    <input id="register-name" name="name" type="text" placeholder="Enter your name"
                        autocomplete="name" maxlength="100" required>
                </div>

                <div class="field-group">
                    <label for="register-email">Email</label>
                    <input id="register-email" name="email" type="email" placeholder="Enter your email"
                        autocomplete="email" required>
                </div>

                <div class="field-group">
                    <label for="register-password">Password</label>
                    <input id="register-password" name="password" type="password" placeholder="At least 8 characters"
                        autocomplete="new-password" minlength="8" maxlength="128" required>
                </div>

                <button type="submit" name="register" class="btn-primary">Register</button>

                <p class="auth-switch">
                    Already have an account?
                    <button type="button" class="link-btn" data-show="login-form">Login</button>
                </p>
            </form>
        </div>
    </main>

    <script src="assets/js/app.js"></script>
</body>

</html>
