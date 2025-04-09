<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'gymbridges');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE userID='$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Welcome</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="custom.css" rel="stylesheet">
    <style>
        .avatar {
            max-width: 150px;
            max-height: 200px;
            width: auto;
            height: auto;
        }
        .avatar-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }
        .file-input-wrapper input[type="file"] {
            font-size: 100px;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
        }
        .btn-file-input {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            height: 38px;
            line-height: 22px;
            width: 150px;
        }
        .btn-file-input:hover {
            background-color: #218838;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            text-align: center;
            height: 38px;
            line-height: 22px;
            width: 150px;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .input-group {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        .btn-danger {
            width: 150px;
            margin-top: 10px;
        }
    </style>
    <script>
        function updateFileName(input) {
            var fileName = input.files[0].name;
            var label = input.nextElementSibling;
            label.textContent = fileName;
        }
    </script>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message_type']; ?>" role="alert">
                    <?php echo $_SESSION['message']; ?>
                    <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                </div>
            <?php endif; ?>
            <h1 class="mt-3">Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
            <div class="avatar-container mt-3">
                <img src="<?php echo htmlspecialchars($user['avatar']); ?>" class="avatar" alt="Avatar">
                <form method="POST" action="upload_avatar.php" enctype="multipart/form-data" class="mt-3">
                    <div class="input-group">
                        <div class="file-input-wrapper">
                            <input type="file" name="avatar" id="avatar" required onchange="updateFileName(this)">
                            <label for="avatar" class="btn btn-file-input">Choose Image</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
                <a href="upload_avatar.php?delete_avatar=true" class="btn btn-danger">Delete Avatar</a>
            </div>
            <p class="mt-3">Email: <?php echo htmlspecialchars($user['email']); ?></p>
            <p>Registration Date: <?php echo htmlspecialchars($user['registrationDate']); ?></p>
            <p>Last Login Date: <?php echo htmlspecialchars($user['lastLoginDate']); ?></p>
            <p>Premium Status: <?php echo $user['isPremium'] ? 'Yes' : 'No'; ?></p>
            <p>Weight: <?php echo htmlspecialchars($user['weight']); ?> kg</p>
            <p>Gender: <?php echo htmlspecialchars($user['gender']); ?></p>
            <p>Age: <?php echo htmlspecialchars($user['age']); ?></p>
            <p>Height: <?php echo htmlspecialchars($user['height']); ?> cm</p>
            <a href="todo.php" class="btn btn-primary mt-3">To-Do List</a><br>
            <a href="notes.php" class="btn btn-primary mt-3">Notes</a><br>
            <a href="progress.php" class="btn btn-primary mt-3">Track Progress</a><br>
            <a href="index.php" class="btn btn-primary mt-3">To main</a><br>
            <a href="logout.php" class="btn btn-secondary mt-3">Logout</a>
        </div>
    </div>
</div>
</body>
</html>
