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

$message = '';
$message_type = '';

// Обработка добавления задачи
$user_id = $_SESSION['user_id'];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task']) && !isset($_POST['task_id'])) {
    $task = $_POST['task'];
    $sql = "INSERT INTO tasks (user_id, task) VALUES ('$user_id', '$task')";
    if ($conn->query($sql) === TRUE) {
        $message = "Task added successfully.";
        $message_type = "success";
    } else {
        $message = "Error: " . $conn->error;
        $message_type = "danger";
    }
}

// Обработка удаления задачи
if (isset($_GET['delete'])) {
    $task_id = $_GET['delete'];
    $sql = "DELETE FROM tasks WHERE id='$task_id' AND user_id='$user_id'";
    if ($conn->query($sql) === TRUE) {
        $message = "Task deleted successfully.";
        $message_type = "success";
    } else {
        $message = "Error: " . $conn->error;
        $message_type = "danger";
    }
}

// Обработка редактирования задачи
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task_id']) && isset($_POST['task'])) {
    $task_id = $_POST['task_id'];
    $task = $_POST['task'];
    $sql = "UPDATE tasks SET task='$task' WHERE id='$task_id' AND user_id='$user_id'";
    if ($conn->query($sql) === TRUE) {
        $message = "Task updated successfully.";
        $message_type = "success";
    } else {
        $message = "Error: " . $conn->error;
        $message_type = "danger";
    }
}

// Обработка отметки выполнения задачи
if (isset($_GET['complete'])) {
    $task_id = $_GET['complete'];
    $sql = "UPDATE tasks SET is_completed = NOT is_completed WHERE id='$task_id' AND user_id='$user_id'";
    if ($conn->query($sql) === TRUE) {
        $message = "Task status updated successfully.";
        $message_type = "success";
    } else {
        $message = "Error: " . $conn->error;
        $message_type = "danger";
    }
}

// Фильтрация задач
$order_by = "created_at DESC";
$current_filter = isset($_GET['filter']) ? $_GET['filter'] : 'date_desc';
$status_filter = isset($_GET['status_filter']) ? $_GET['status_filter'] : '';

switch ($current_filter) {
    case "date_asc":
        $order_by = "created_at ASC";
        break;
    case "date_desc":
        $order_by = "created_at DESC";
        break;
    case "length_asc":
        $order_by = "CHAR_LENGTH(task) ASC";
        break;
    case "length_desc":
        $order_by = "CHAR_LENGTH(task) DESC";
        break;
    case "status":
        $order_by = "is_completed DESC, created_at DESC";
        break;
    case "alphabetical":
        $order_by = "task ASC";
        break;
}

$status_condition = "";
if ($status_filter == 'completed') {
    $status_condition = "AND is_completed = 1";
} elseif ($status_filter == 'not_completed') {
    $status_condition = "AND is_completed = 0";
}

$sql = "SELECT * FROM tasks WHERE user_id='$user_id' $status_condition ORDER BY $order_by";
$result = $conn->query($sql);
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>To-Do List</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="custom.css" rel="stylesheet">
    <style>
        .task-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .task-input {
            flex-grow: 1;
            margin-right: 10px;
        }
        .task-buttons {
            display: flex;
            gap: 10px;
        }
        .completed {
            text-decoration: line-through;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="text-center">To-Do List for <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>" role="alert">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="todo.php" class="mb-4">
                <div class="input-group">
                    <input type="text" name="task" class="form-control" placeholder="New task" required>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">Add Task</button>
                    </div>
                </div>
            </form>
            <form method="GET" action="todo.php" class="mb-4">
                <div class="input-group">
                    <select name="filter" class="form-control">
                        <option value="date_desc" <?php if ($current_filter == 'date_desc') echo 'selected'; ?>>Newest First</option>
                        <option value="date_asc" <?php if ($current_filter == 'date_asc') echo 'selected'; ?>>Oldest First</option>
                        <option value="length_asc" <?php if ($current_filter == 'length_asc') echo 'selected'; ?>>Shortest First</option>
                        <option value="length_desc" <?php if ($current_filter == 'length_desc') echo 'selected'; ?>>Longest First</option>
                        <option value="status" <?php if ($current_filter == 'status') echo 'selected'; ?>>By Status</option>
                        <option value="alphabetical" <?php if ($current_filter == 'alphabetical') echo 'selected'; ?>>Alphabetical</option>
                    </select>
                    <select name="status_filter" class="form-control ml-2">
                        <option value="" <?php if ($status_filter == '') echo 'selected'; ?>>All</option>
                        <option value="completed" <?php if ($status_filter == 'completed') echo 'selected'; ?>>Completed</option>
                        <option value="not_completed" <?php if ($status_filter == 'not_completed') echo 'selected'; ?>>Not Completed</option>
                    </select>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-secondary">Filter</button>
                    </div>
                </div>
            </form>
            <ul class="list-group">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li class="list-group-item">
                        <form method="POST" action="todo.php" class="task-item">
                            <input type="hidden" name="task_id" value="<?php echo $row['id']; ?>">
                            <input type="text" name="task" class="form-control task-input <?php echo $row['is_completed'] ? 'completed' : ''; ?>" value="<?php echo htmlspecialchars($row['task']); ?>" required>
                            <div class="task-buttons">
                                <a href="todo.php?complete=<?php echo $row['id']; ?>" class="btn btn-<?php echo $row['is_completed'] ? 'secondary' : 'info'; ?> btn-sm"><?php echo $row['is_completed'] ? 'Uncomplete' : 'Complete'; ?></a>
                                <button type="submit" class="btn btn-success btn-sm">Update</button>
                                <a href="todo.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                            </div>
                        </form>
                    </li>
                <?php endwhile; ?>
            </ul>
            <a href="welcome.php" class="btn btn-secondary mt-3">Back to Profile</a>
            <a href="logout.php" class="btn btn-secondary mt-3">Logout</a>
        </div>
    </div>
</div>
</body>
</html>
