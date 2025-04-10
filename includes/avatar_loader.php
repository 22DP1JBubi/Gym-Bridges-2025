<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$avatarPath = 'images/default_avatar.png'; // дефолтный путь

if (isset($_SESSION['user_id'])) {
    $conn = new mysqli('localhost', 'root', '', 'gymbridges');
    if (!$conn->connect_error) {
        $user_id = $_SESSION['user_id'];
        $result = $conn->query("SELECT avatar FROM users WHERE userID = $user_id");
        if ($result && $row = $result->fetch_assoc()) {
            if (!empty($row['avatar'])) {
                $avatarPath = htmlspecialchars($row['avatar']);
            }
        }
        $conn->close();
    }
}
?>
