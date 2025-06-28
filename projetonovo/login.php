<?php
require_once 'includes/auth.php';

$auth = new Auth();
$message = '';


if ($auth->isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $result = $auth->login($_POST['username'], $_POST['password']);
        if ($result['success']) {
            header('Location: dashboard.php');
            exit();
        } else {
            $message = $result['message'];
        }
    } elseif (isset($_POST['register'])) {
        $result = $auth->register($_POST['username'], $_POST['password']);
        $message = $result['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Construction Store Management - Login</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="login-container">
        <h1 class="login-title">Construction Store Management</h1>
        
        <?php if ($message): ?>
            <div class="alert <?php echo strpos($message, 'successful') !== false ? 'alert-success' : 'alert-error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="nav-tabs mb-2">
            <button class="nav-tab active" onclick="showLogin()">Login</button>
            <button class="nav-tab" onclick="showRegister()">Register</button>
        </div>
        
        
        <form id="loginForm" method="POST">
            <div class="form-group">
                <label class="form-label">Username:</label>
                <input type="text" name="username" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Password:</label>
                <input type="password" name="password" class="form-input" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary" style="width: 100%;">Login</button>
        </form>
        
       
        <form id="registerForm" method="POST" class="hidden">
            <div class="form-group">
                <label class="form-label">Username:</label>
                <input type="text" name="username" class="form-input" required minlength="3">
                <small>Minimum 3 characters</small>
            </div>
            <div class="form-group">
                <label class="form-label">Password:</label>
                <input type="password" name="password" class="form-input" required minlength="6">
                <small>Minimum 6 characters</small>
            </div>
            <button type="submit" name="register" class="btn btn-success" style="width: 100%;">Register</button>
        </form>
    </div>
    
    <script>
        function showLogin() {
            document.getElementById('loginForm').classList.remove('hidden');
            document.getElementById('registerForm').classList.add('hidden');
            document.querySelectorAll('.nav-tab')[0].classList.add('active');
            document.querySelectorAll('.nav-tab')[1].classList.remove('active');
        }
        
        function showRegister() {
            document.getElementById('loginForm').classList.add('hidden');
            document.getElementById('registerForm').classList.remove('hidden');
            document.querySelectorAll('.nav-tab')[0].classList.remove('active');
            document.querySelectorAll('.nav-tab')[1].classList.add('active');
        }
    </script>
</body>
</html>
