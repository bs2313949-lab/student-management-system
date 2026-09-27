<?php
require_once __DIR__ . '/db.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $user = getUserByUsername($pdo, $username);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        redirect('dashboard.php');
    }

    $loginError = 'Invalid username or password.';
} else {
    $loginError = '';
}
?>

<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Student Management System</title>
    <link rel='preconnect' href='https://fonts.googleapis.com'>
    <link rel='preconnect' href='https://fonts.gstatic.com' crossorigin>
    <link href='https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap' rel='stylesheet'>
    <link rel='stylesheet' href='styles.css'>
</head>
<body class='login-body'>
    <div class='login-wrapper'>
        <div class='login-card'>
            <div class='login-brand'>
                <span class='brand-mark'>S</span>
                <h1>StudentHub</h1>
            </div>

            <p class='login-subtitle'>Sign in to your student management portal</p>

            <?php if (!empty($loginError)): ?>
                <div class='alert alert-error'><?= htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <form method='POST' class='login-form'>
                <input type='hidden' name='login' value='1'>

                <label>
                    Username
                    <input type='text' name='username' placeholder='Enter username' required>
                </label>

                <label>
                    Password
                    <input type='password' name='password' placeholder='Enter password' required>
                </label>

                <button type='submit' class='btn btn-primary btn-full'>Login</button>
            </form>

            <div class='demo-credentials'>
                <p>Demo admin: admin / admin123</p>
                <p>Demo student: student / student123</p>
            </div>
        </div>
    </div>

    <script src='script.js'></script>
</body>
</html>
