<?php
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'superadmin'])) {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "gymbridges");
mysqli_set_charset($conn, "utf8mb4");

// Обновление isPremium
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_premium'])) {
    $id = intval($_POST['user_id']);
    $isPremium = intval($_POST['isPremium']);
    $conn->query("UPDATE users SET isPremium = $isPremium WHERE userID = $id");
    header("Location: manage_users.php");
    exit();
}

// Обновление role (только для superadmin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_role']) && $_SESSION['role'] === 'superadmin') {
    $id = intval($_POST['user_id']);
    $role = $_POST['role'];
    if (in_array($role, ['user', 'admin', 'superadmin'])) {
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE userID = ?");
        $stmt->bind_param("si", $role, $id);
        $stmt->execute();
    }
    header("Location: manage_users.php");
    exit();
}

// Выбор всех пользователей
$users = $conn->query("SELECT userID, username, email, isPremium, role FROM users");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'includes/admin_sidebar.php'; ?>
<div class="main-content" style="margin-left: 220px; padding: 30px;">
    <h2 class="mb-4">User Management</h2>
    <table class="table table-bordered bg-white">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Premium</th>
                <th>Change Premium</th>
                <th>Role</th>
                <?php if ($_SESSION['role'] === 'superadmin'): ?>
                    <th>Change Role</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php while ($user = $users->fetch_assoc()): ?>
            <tr>
                <td><?= $user['userID'] ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= $user['isPremium'] ? 'Yes' : 'No' ?></td>
                <td>
                    <form method="POST" class="d-flex">
                        <input type="hidden" name="user_id" value="<?= $user['userID'] ?>">
                        <select name="isPremium" class="form-select form-select-sm me-2">
                            <option value="0" <?= $user['isPremium'] == 0 ? 'selected' : '' ?>>No</option>
                            <option value="1" <?= $user['isPremium'] == 1 ? 'selected' : '' ?>>Yes</option>
                        </select>
                        <button name="change_premium" class="btn btn-sm btn-primary">Update</button>
                    </form>
                </td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <?php if ($_SESSION['role'] === 'superadmin'): ?>
                    <td>
                        <form method="POST" class="d-flex">
                            <input type="hidden" name="user_id" value="<?= $user['userID'] ?>">
                            <select name="role" class="form-select form-select-sm me-2">
                                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="superadmin" <?= $user['role'] === 'superadmin' ? 'selected' : '' ?>>Superadmin</option>
                            </select>
                            <button name="change_role" class="btn btn-sm btn-warning">Change</button>
                        </form>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</div>
</body>
</html>
