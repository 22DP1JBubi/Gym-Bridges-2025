<?php
session_start(); // Стартуем сессию для проверки ошибок
if (isset($_GET['redirect'])) {
    $_SESSION['redirect'] = $_GET['redirect'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: url("images/background.jpg") no-repeat center center fixed;
            background-size: cover;
        }
        .login-container {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .login-container .form-group {
            margin-bottom: 1rem;
        }
        .error-message {
            color: red;
            margin-top: 0.5rem;
            font-size: 0.9rem;
        }
        .register-link {
            text-align: center;
            margin-top: 1rem;
        }
        .main-link {
            text-align: center;
            margin-top: 10px;
            font-size: 14px;
        }
</style>

</head>
<body>

<div class="login-container">
    <h2 class="text-center mb-4">Login</h2>

    <form action="login_process.php" method="POST" onsubmit="return validateForm()">
        <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_GET['redirect'] ?? ($_SESSION['redirect'] ?? '')); ?>">

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="error-message"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </div>
    </form>

    <div class="register-link">
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
    <div class="main-link">
      <a href="index.html">Go to main page</a>
    </div>
</div>

<script>
    function validateForm() {
        const username = document.getElementById("username").value.trim();
        const password = document.getElementById("password").value.trim();

        if (!username || !password) {
            alert("Please fill in both username and password.");
            return false;
        }
        return true;
    }
</script>

</body>
</html>
