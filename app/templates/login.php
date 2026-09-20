<?php
require_once '../lib/Auth.php';

// If already logged in, redirect to dashboard
if (Auth::isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (Auth::login($username, $password)) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid credentials';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Noodu Slide Generator - Login</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #3366ff 0%, #0d203f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 60px rgba(13, 32, 63, 0.3);
        }
        .logo { text-align: center; margin-bottom: 30px; }
        .logo h1 { font-size: 28px; color: #0d203f; margin-bottom: 8px; }
        .logo p { color: #8a93a5; font-size: 14px; }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #0d203f;
            margin-bottom: 8px;
        }
        input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e3e6ec;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.2s;
        }
        input:focus {
            outline: none;
            border-color: #3366ff;
            box-shadow: 0 0 0 3px rgba(51, 102, 255, 0.1);
        }
        .error {
            background: #fff0e5;
            color: #c2571a;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        button {
            width: 100%;
            padding: 12px 16px;
            background: #3366ff;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        button:hover { background: #254dd6; }
        .demo { text-align: center; margin-top: 20px; font-size: 13px; color: #8a93a5; }
        .demo code { background: #fafaf6; padding: 2px 6px; border-radius: 4px; font-family: monospace; }
    </style>
</head>
<body>
<div class="login-container">
    <div class="logo">
        <h1>Noodu</h1>
        <p>Slide Generator</p>
    </div>

    <?php if (!empty($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required autofocus>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Login</button>
    </form>

    <div class="demo">
        Demo: <code>admin</code> / <code>@noodu2026</code>
    </div>
</div>
</body>
</html>
