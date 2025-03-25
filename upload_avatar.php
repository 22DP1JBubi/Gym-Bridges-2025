<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'gymbridges');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$upload_dir = 'uploads/avatars/';
$default_avatar = 'uploads/avatars/default_avatar.png';

// Проверка существования папки и создание её, если не существует
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
    $avatar_name = basename($_FILES['avatar']['name']);
    $avatar_path = $upload_dir . $avatar_name;
    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $avatar_path)) {
        // Удаляем старую аватарку, если она не дефолтная
        $sql = "SELECT avatar FROM users WHERE userID='$user_id'";
        $result = $conn->query($sql);
        $user = $result->fetch_assoc();
        if ($user['avatar'] && $user['avatar'] != $default_avatar) {
            unlink($user['avatar']);
        }
        
        // Обновляем путь к новой аватарке в базе данных
        $sql = "UPDATE users SET avatar='$avatar_path' WHERE userID='$user_id'";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = "Avatar updated successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error: " . $conn->error;
            $_SESSION['message_type'] = "danger";
        }
    } else {
        $_SESSION['message'] = "Error uploading image.";
        $_SESSION['message_type'] = "danger";
    }
    header("Location: welcome.php");
    exit();
}

// Обработка удаления аватарки
if (isset($_GET['delete_avatar'])) {
    $sql = "SELECT avatar FROM users WHERE userID='$user_id'";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();
    if ($user['avatar'] && $user['avatar'] != $default_avatar) {
        unlink($user['avatar']);
    }

    $sql = "UPDATE users SET avatar='$default_avatar' WHERE userID='$user_id'";
    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Avatar deleted successfully.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error: " . $conn->error;
        $_SESSION['message_type'] = "danger";
    }
    header("Location: welcome.php");
    exit();
}

$conn->close();
?>
