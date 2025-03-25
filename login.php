<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'gymbridges');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
$message_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['userID'];
            $_SESSION['username'] = $user['username'];

            // Обновление даты последнего логина
            $lastLoginDate = date('Y-m-d');
            $update_sql = "UPDATE users SET lastLoginDate='$lastLoginDate' WHERE userID='" . $user['userID'] . "'";
            $conn->query($update_sql);

            header("Location: welcome.php");
            exit();
        } else {
            $message = "Invalid password.";
            $message_type = "danger";
        }
    } else {
        $message = "No user found.";
        $message_type = "danger";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="custom.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h2>Login</h2>
                </div>
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $message_type; ?>" role="alert">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="login.php">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <p>Don't have an account? <a href="register.php">Register here</a></p>
                    <p>To main <a href="index.html">LETS'GO</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
