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

$upload_dir = 'uploads/'; // Папка для хранения загруженных изображений

// Проверка существования папки и создание её, если не существует
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Обработка добавления заметки
$user_id = $_SESSION['user_id'];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['note']) && !isset($_POST['note_id'])) {
    $note = $_POST['note'];
    $is_important = isset($_POST['is_important']) ? 1 : 0;
    $image = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = basename($_FILES['image']['name']);
        $image_path = $upload_dir . $image_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
            $image = $image_path;
        } else {
            $message = "Error uploading image.";
            $message_type = "danger";
        }
    }

    $stmt = $conn->prepare("INSERT INTO notes (user_id, note, is_important, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('isis', $user_id, $note, $is_important, $image);
    if ($stmt->execute()) {
        $message = "Note added successfully.";
        $message_type = "success";
    } else {
        $message = "Error: " . $stmt->error;
        $message_type = "danger";
    }
    $stmt->close();
}

// Обработка удаления заметки
if (isset($_GET['delete'])) {
    $note_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
    $stmt->bind_param('ii', $note_id, $user_id);
    if ($stmt->execute()) {
        $message = "Note deleted successfully.";
        $message_type = "success";
    } else {
        $message = "Error: " . $stmt->error;
        $message_type = "danger";
    }
    $stmt->close();
}

// Обработка редактирования заметки
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['note_id']) && isset($_POST['note'])) {
    $note_id = $_POST['note_id'];
    $note = $_POST['note'];
    $is_important = isset($_POST['is_important']) ? 1 : 0;
    $image = null;

    // Проверяем, загружено ли новое изображение
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = basename($_FILES['image']['name']);
        $image_path = $upload_dir . $image_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
            $image = $image_path;
        } else {
            $message = "Error uploading image.";
            $message_type = "danger";
        }
    } else {
        // Если новое изображение не загружено, оставляем старое
        $stmt = $conn->prepare("SELECT image FROM notes WHERE id = ? AND user_id = ?");
        $stmt->bind_param('ii', $note_id, $user_id);
        $stmt->execute();
        $stmt->bind_result($current_image);
        $stmt->fetch();
        $stmt->close();
        $image = $current_image;
    }

    $stmt = $conn->prepare("UPDATE notes SET note = ?, is_important = ?, image = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param('sisii', $note, $is_important, $image, $note_id, $user_id);
    if ($stmt->execute()) {
        $message = "Note updated successfully.";
        $message_type = "success";
    } else {
        $message = "Error: " . $stmt->error;
        $message_type = "danger";
    }
    $stmt->close();
}

// Обработка удаления изображения
if (isset($_GET['delete_image'])) {
    $note_id = $_GET['delete_image'];
    $stmt = $conn->prepare("UPDATE notes SET image = NULL WHERE id = ? AND user_id = ?");
    $stmt->bind_param('ii', $note_id, $user_id);
    if ($stmt->execute()) {
        $message = "Image deleted successfully.";
        $message_type = "success";
    } else {
        $message = "Error: " . $stmt->error;
        $message_type = "danger";
    }
    $stmt->close();
}

// Обработка отметки важности
if (isset($_GET['important'])) {
    $note_id = $_GET['important'];
    $stmt = $conn->prepare("UPDATE notes SET is_important = NOT is_important WHERE id = ? AND user_id = ?");
    $stmt->bind_param('ii', $note_id, $user_id);
    if ($stmt->execute()) {
        $message = "Note importance updated successfully.";
        $message_type = "success";
    } else {
        $message = "Error: " . $stmt->error;
        $message_type = "danger";
    }
    $stmt->close();
}

// Обработка сортировки и фильтрации заметок
$order_by = "created_at DESC";
$selected_sort = 'created_desc'; // По умолчанию сортировка по новизне
$filter_condition = "1=1";

if (isset($_GET['sort'])) {
    $selected_sort = $_GET['sort'];
    switch ($selected_sort) {
        case 'length_asc':
            $order_by = "LENGTH(note) ASC";
            break;
        case 'length_desc':
            $order_by = "LENGTH(note) DESC";
            break;
        case 'created_asc':
            $order_by = "created_at ASC";
            break;
        case 'created_desc':
            $order_by = "created_at DESC";
            break;
        case 'updated_asc':
            $order_by = "updated_at ASC";
            break;
        case 'updated_desc':
            $order_by = "updated_at DESC";
            break;
    }
}

if (isset($_GET['has_image'])) {
    if ($_GET['has_image'] == '1') {
        $filter_condition .= " AND image IS NOT NULL";
    } elseif ($_GET['has_image'] == '0') {
        $filter_condition .= " AND image IS NULL";
    }
}

if (isset($_GET['is_important'])) {
    if ($_GET['is_important'] == '1') {
        $filter_condition .= " AND is_important = 1";
    } elseif ($_GET['is_important'] == '0') {
        $filter_condition .= " AND is_important = 0";
    }
}

$sql = "SELECT * FROM notes WHERE user_id = ? AND $filter_condition ORDER BY $order_by";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Notes</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="custom.css" rel="stylesheet">
    <style>
        .note-item {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
        }
        .note-input {
            width: 100%;
            resize: vertical;
            margin-bottom: 10px;
        }
        .note-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            flex-wrap: wrap;
        }
        .note-textarea {
            width: 100%;
            resize: vertical;
            min-height: 150px;
        }
        .important {
            background-color: #ffefc1;
        }
        .note-image {
            max-width: 100%;
            height: auto;
            margin-bottom: 10px;
        }
        /* Стилизация кнопки загрузки файла */
        .custom-file-input {
            display: none;
        }
        .custom-file-label {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 5px;
            width: 100%;
            text-align: center;
        }
        .custom-file-label:hover {
            background-color: #218838;
        }
        @media (max-width: 576px) {
            .note-buttons {
                justify-content: center;
                flex-direction: column;
            }
            .note-buttons .btn, .note-buttons input[type="checkbox"], .note-buttons label {
                width: 100%;
                margin-bottom: 5px;
                text-align: center; /* Центрирование текста */
            }
        }
    </style>
    <script>
        function adjustTextareaHeight(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = (textarea.scrollHeight) + 'px';
        }
        document.addEventListener('DOMContentLoaded', function () {
            const textareas = document.querySelectorAll('textarea');
            textareas.forEach(textarea => {
                adjustTextareaHeight(textarea);
                textarea.addEventListener('input', function () {
                    adjustTextareaHeight(textarea);
                });
            });

            // Показать имя выбранного файла
            const fileInputs = document.querySelectorAll('.custom-file-input');
            fileInputs.forEach(fileInput => {
                const label = fileInput.nextElementSibling;
                fileInput.addEventListener('change', function () {
                    const fileName = fileInput.files[0].name;
                    label.textContent = fileName;
                });
            });
        });
    </script>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="text-center">Notes for <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>" role="alert">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="notes.php" class="mb-4" enctype="multipart/form-data">
                <div class="input-group">
                    <textarea name="note" class="form-control note-textarea" placeholder="New note" required></textarea>
                </div>
                <div class="input-group mt-2">
                    <input type="file" name="image" id="image" class="custom-file-input">
                    <label for="image" class="custom-file-label">Choose Image</label>
                </div>
                <div class="input-group mt-2">
                    <input type="checkbox" name="is_important" id="is_important">
                    <label for="is_important" class="ml-2">Mark as Important</label>
                </div>
                <div class="input-group-append mt-2">
                    <button type="submit" class="btn btn-primary">Add Note</button>
                </div>
            </form>

            <form method="GET" action="notes.php" class="mb-4">
                <div class="input-group">
                    <select name="sort" class="form-control">
                        <option value="created_desc" <?php if ($selected_sort == 'created_desc') echo 'selected'; ?>>Newest First</option>
                        <option value="created_asc" <?php if ($selected_sort == 'created_asc') echo 'selected'; ?>>Oldest First</option>
                        <option value="length_asc" <?php if ($selected_sort == 'length_asc') echo 'selected'; ?>>Shortest First</option>
                        <option value="length_desc" <?php if ($selected_sort == 'length_desc') echo 'selected'; ?>>Longest First</option>
                        <option value="updated_asc" <?php if ($selected_sort == 'updated_asc') echo 'selected'; ?>>Least Recently Updated</option>
                        <option value="updated_desc" <?php if ($selected_sort == 'updated_desc') echo 'selected'; ?>>Most Recently Updated</option>
                    </select>
                    <select name="has_image" class="form-control ml-2">
                        <option value="">All</option>
                        <option value="1" <?php if (isset($_GET['has_image']) && $_GET['has_image'] == '1') echo 'selected'; ?>>With Image</option>
                        <option value="0" <?php if (isset($_GET['has_image']) && $_GET['has_image'] == '0') echo 'selected'; ?>>Without Image</option>
                    </select>
                    <select name="is_important" class="form-control ml-2">
                        <option value="">All</option>
                        <option value="1" <?php if (isset($_GET['is_important']) && $_GET['is_important'] == '1') echo 'selected'; ?>>Important</option>
                        <option value="0" <?php if (isset($_GET['is_important']) && $_GET['is_important'] == '0') echo 'selected'; ?>>Not Important</option>
                    </select>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-secondary">Filter</button>
                    </div>
                </div>
            </form>

            <ul class="list-group">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li class="list-group-item <?php echo $row['is_important'] ? 'important' : ''; ?>">
                        <form method="POST" action="notes.php" class="note-item" enctype="multipart/form-data">
                            <input type="hidden" name="note_id" value="<?php echo $row['id']; ?>">
                            <textarea name="note" class="form-control note-input" required><?php echo htmlspecialchars($row['note']); ?></textarea>
                            <?php if ($row['image']): ?>
                                <img src="<?php echo $row['image']; ?>" class="note-image">
                                <a href="notes.php?delete_image=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Delete Image</a>
                            <?php endif; ?>
                            <div class="row mt-2">
                                <div class="col-12 col-sm-6 mb-2">
                                    <input type="file" name="image" id="image_<?php echo $row['id']; ?>" class="custom-file-input">
                                    <label for="image_<?php echo $row['id']; ?>" class="custom-file-label">Choose Image</label>
                                </div>
                                <div class="col-12 col-sm-6 note-buttons">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <input type="checkbox" name="is_important" id="is_important_<?php echo $row['id']; ?>" <?php echo $row['is_important'] ? 'checked' : ''; ?>>
                                        <label for="is_important_<?php echo $row['id']; ?>" class="mr-2">Important</label>
                                    </div>
                                    <button type="submit" class="btn btn-success btn-sm">Update</button>
                                    <a href="notes.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                                    <a href="notes.php?important=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Toggle Important</a>
                                </div>
                            </div>
                        </form>
                        <small>Created at: <?php echo $row['created_at']; ?> | Last updated: <?php echo $row['updated_at']; ?></small>
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
