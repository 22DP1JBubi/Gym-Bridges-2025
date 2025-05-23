<?php
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'superadmin'])) {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "gymbridges");
mysqli_set_charset($conn, "utf8mb4");

// Обработка подтверждения или отклонения
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['trainer_id'], $_POST['action'])) {
    $trainer_id = intval($_POST['trainer_id']);
    $action = $_POST['action'];
    if (in_array($action, ['approved', 'rejected', 'pending'])) {
        $stmt = $conn->prepare("UPDATE trainers SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $action, $trainer_id);
        $stmt->execute();
    }
    header("Location: manage_trainers.php");
    exit();
}




// Получаем всех тренеров, сортируя по статусу: pending первыми
$trainers = $conn->query("SELECT * FROM trainers ORDER BY FIELD(status, 'pending', 'approved', 'rejected'), created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Trainers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-trainer {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .status-badge {
            font-size: 0.85rem;
            padding: 5px 10px;
            border-radius: 10px;
        }
    </style>
</head>
<body class="bg-light">
<?php include 'includes/admin_sidebar.php'; ?>
<div class="main-content" style="margin-left: 220px; padding: 30px;">
    <h2 class="mb-4">Trainer Applications</h2>
    <div class="row">
        <?php while ($trainer = $trainers->fetch_assoc()): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card card-trainer p-3">
                    <h5><?= htmlspecialchars($trainer['first_name'] . ' ' . $trainer['last_name']) ?></h5>
                    <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($trainer['email']) ?></p>
                    <p class="mb-1"><strong>Phone:</strong> <?= htmlspecialchars($trainer['phone']) ?></p>
                    <p class="mb-1"><strong>City:</strong> <?= htmlspecialchars($trainer['city']) ?>, <?= htmlspecialchars($trainer['country']) ?></p>
                    <p class="mb-2">
                        <strong>Status:</strong>
                        <span class="badge bg-<?= $trainer['status'] === 'approved' ? 'success' : ($trainer['status'] === 'rejected' ? 'danger' : 'warning') ?>">
                            <?= ucfirst($trainer['status']) ?>
                        </span>

                        <span class="status-badge bg-
                            <?php
                                echo $trainer['status'] === 'pending' ? 'warning' :
                                     ($trainer['status'] === 'approved' ? 'success' : 'secondary');
                            ?> text-white">
                            <?= ucfirst($trainer['status']) ?>
                        </span>
                    </p>

                    <form method="POST" class="d-flex align-items-center gap-2 mt-2">
                        <input type="hidden" name="trainer_id" value="<?= $trainer['id'] ?>">
                        <select name="action" class="form-select form-select-sm" style="max-width: 140px;">
                            <option value="pending" <?= $trainer['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="approved" <?= $trainer['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                            <option value="rejected" <?= $trainer['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                        <button class="btn btn-sm btn-outline-success" type="submit">Update</button>
                    </form>


                    <a href="trainer_view.php?id=<?= $trainer['id'] ?>" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-eye"></i> View
                    </a>

                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>
