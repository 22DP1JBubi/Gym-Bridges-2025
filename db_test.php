<?php
// Подключение к базе
$conn = new mysqli('localhost', 'root', '', 'gymbridges');

// Проверка подключения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Устанавливаем UTF-8
$conn->set_charset("utf8mb4");

// Выполнение запроса
$result = $conn->query("SELECT userID, username, email FROM users");

// Проверка результата
if (!$result) {
    die("Ошибка запроса: " . $conn->error);
}

// Вывод списка
echo "<h2>Список пользователей:</h2><ul>";
while ($row = $result->fetch_assoc()) {
    echo "<li><strong>ID:</strong> {$row['userID']} | <strong>Username:</strong> {$row['username']} | <strong>Email:</strong> {$row['email']}</li>";
}
echo "</ul>";

// Закрытие подключения
$conn->close();
?>
