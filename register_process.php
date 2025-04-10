<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'gymbridges');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$username = trim($_POST['username']);
$email = trim($_POST['email']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$weight = trim($_POST['weight']);
$gender = trim($_POST['gender']);
$age = trim($_POST['age']);
$height = trim($_POST['height']);

if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($weight) || empty($gender) || empty($age) || empty($height)) {
    $_SESSION['error'] = "All fields are required.";
    header("Location: register.php");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Invalid email format.";
    header("Location: register.php");
    exit();
}

if ($password !== $confirm_password) {
    $_SESSION['error'] = "Passwords do not match.";
    header("Location: register.php");
    exit();
}

if (!is_numeric($weight) || $weight < 5 || $weight > 250 || !is_numeric($age) || $age < 1 || $age > 120 || !is_numeric($height) || $height < 30 || $height > 250) {
    $_SESSION['error'] = "Invalid numeric values.";
    header("Location: register.php");
    exit();
}

// Проверка уникальности username
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    $_SESSION['error'] = "Username already exists.";
    header("Location: register.php");
    exit();
}
$stmt->close();

$hashed = password_hash($password, PASSWORD_DEFAULT);
$regDate = date('Y-m-d');


$defaultAvatar = 'images/default_avatar.png';
$stmt = $conn->prepare("INSERT INTO users (username, email, password, weight, gender, age, height, registrationDate, avatar) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssdsidss", $username, $email, $hashed, $weight, $gender, $age, $height, $regDate, $defaultAvatar);

if ($stmt->execute()) {
    $_SESSION['success'] = "Registration successful!";
    header("Location: login.php");
} else {
    $_SESSION['error'] = "Something went wrong. Try again.";
    header("Location: register.php");
}
$stmt->close();
$conn->close();
