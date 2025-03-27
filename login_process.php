<?php
session_start();

$redirect = $_POST['redirect'] ?? $_SESSION['redirect'] ?? 'welcome.php';

$host = "localhost";
$user = "root";
$password = "";
$database = "gymbridges";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    $_SESSION['error'] = "Database connection failed.";
    header("Location: login.php?redirect=" . urlencode($redirect));
    exit();
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($username) || empty($password)) {
    $_SESSION['error'] = "Both username and password are required.";
    header("Location: login.php?redirect=" . urlencode($redirect));
    exit();
}

$stmt = $conn->prepare("SELECT userID, username, password FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['userID'];
        $_SESSION['username'] = $user['username'];

        $date = date('Y-m-d');
        $update = $conn->prepare("UPDATE users SET lastLoginDate = ? WHERE userID = ?");
        $update->bind_param("si", $date, $user['userID']);
        $update->execute();

        unset($_SESSION['redirect']);
        header("Location: welcome.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid username or password.";
        header("Location: login.php?redirect=" . urlencode($redirect));
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid username or password.";
    header("Location: login.php?redirect=" . urlencode($redirect));
    exit();
}