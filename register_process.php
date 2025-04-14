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
$birthdate = $_POST['birthdate'];
$height = trim($_POST['height']);

if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($weight) || empty($gender) || empty($birthdate) || empty($height)) {
    $_SESSION['error'] = "All fields are required.";
    $_SESSION['form_data'] = $_POST;
    header("Location: register.php");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Invalid email format.";
    $_SESSION['form_data'] = $_POST;
    header("Location: register.php");
    exit();
}

if ($password !== $confirm_password) {
    $_SESSION['error'] = "Passwords do not match.";
    $_SESSION['form_data'] = $_POST;
    header("Location: register.php");
    exit();
}

if (!is_numeric($weight) || $weight < 5 || $weight > 250 || !is_numeric($height) || $height < 30 || $height > 250) {
    $_SESSION['error'] = "Invalid numeric values.";
    $_SESSION['form_data'] = $_POST;
    header("Location: register.php");
    exit();
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthdate)) {
    $_SESSION['error'] = "Invalid birthdate format.";
    $_SESSION['form_data'] = $_POST;
    header("Location: register.php");
    exit();
}

$birthDateTime = new DateTime($birthdate);
$today = new DateTime();
$age = $birthDateTime->diff($today)->y;

if ($age < 1 || $age > 120) {
    $_SESSION['error'] = "Age must be between 1 and 120.";
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


if ($gender === 'Male') {
    $defaultAvatar = 'images/default_male_avatar.png';
} elseif ($gender === 'Female') {
    $defaultAvatar = 'images/default_female_avatar.png';
} else {
    $defaultAvatar = 'images/default_other_avatar.png'; // для Other или если не выбрано
}

$isPremium = 0;


$stmt = $conn->prepare("INSERT INTO users (username, email, password, weight, height, gender, registrationDate, birthdate, avatar, isPremium) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssddssssi", $username, $email, $hashed, $weight, $height, $gender, $regDate, $birthdate, $defaultAvatar, $isPremium);



if ($stmt->execute()) {
    $_SESSION['success'] = "Registration successful!";
    header("Location: login.php");
} else {
    $_SESSION['error'] = "Something went wrong. Try again.";
    header("Location: register.php");
}
$stmt->close();
$conn->close();
