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
$default_avatar = $upload_dir . 'default_avatar.png';

// Создание папки, если её нет
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// === ЗАГРУЗКА НОВОЙ АВАТАРКИ ===
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
    $avatar_name = uniqid('avatar_', true) . '.' . pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
    $avatar_path = $upload_dir . $avatar_name;

    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $avatar_path)) {
        // Получение текущей аватарки
        $sql = "SELECT avatar FROM users WHERE userID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        // Удаление старой аватарки (если не дефолтная)
        if (!empty($user['avatar']) && basename($user['avatar']) !== basename($default_avatar)) {
            if (file_exists($user['avatar'])) {
                unlink($user['avatar']);
            }
        }

        // Обновление новой аватарки в БД
        $sql = "UPDATE users SET avatar = ? WHERE userID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $avatar_path, $user_id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['message'] = "Avatar updated successfully.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error uploading image.";
        $_SESSION['message_type'] = "danger";
    }

    header("Location: welcome.php");
    exit();
}

// === УДАЛЕНИЕ АВАТАРКИ ===
if (isset($_GET['delete_avatar'])) {
    $sql = "SELECT avatar FROM users WHERE userID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!empty($user['avatar']) && basename($user['avatar']) !== basename($default_avatar)) {
        if (file_exists($user['avatar'])) {
            unlink($user['avatar']);
        }
    }

    $sql = "UPDATE users SET avatar = ? WHERE userID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $default_avatar, $user_id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['message'] = "Avatar deleted successfully.";
    $_SESSION['message_type'] = "success";
    header("Location: welcome.php");
    exit();
}

$conn->close();
