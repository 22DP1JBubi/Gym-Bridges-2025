<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    $header_conn = new mysqli('localhost', 'root', '', 'user_database');
    if ($header_conn->connect_error) {
        die("Connection failed: " . $header_conn->connect_error);
    }

    $user_id = $_SESSION['user_id'];
    $sql = "SELECT username, avatar FROM users WHERE userID='$user_id'";
    $result = $header_conn->query($sql);
    $header_user = $result->fetch_assoc();

    $header_conn->close();
} else {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .header {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 10px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }
        .header a {
            text-decoration: none;
            color: #007bff;
            margin-left: 10px;
        }
        .header img {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
<div class="header">
    <a href="welcome.php"><?php echo htmlspecialchars($header_user['username']); ?></a>
    <a href="welcome.php">
        <img src="<?php echo htmlspecialchars($header_user['avatar']); ?>" alt="Avatar">
    </a>
</div>
</body>
</html>
