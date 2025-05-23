<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "gymbridges");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$user_id = $_SESSION['user_id'];

include 'includes/avatar_loader.php';

mysqli_set_charset($conn, "utf8mb4");

include 'includes/header.php';

$result = $conn->query("SELECT * FROM trainers WHERE status = 'approved' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Find a Trainer</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    .trainer-card {
      border-radius: 8px;
      box-shadow: 0 4px 14px rgba(0,0,0,0.1);
      transition: all 0.3s ease-in-out;
    }
    .trainer-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 18px rgba(0,0,0,0.15);
    }
    .trainer-img {
      width: 100%;
      height: 220px;
      object-fit: cover;
      border-top-left-radius: 8px;
      border-top-right-radius: 8px;
    }
    .trainer-info {
      padding: 1rem;
    }
    .trainer-name {
      font-size: 1.25rem;
      font-weight: 600;
    }
    .trainer-specialization {
      font-size: 0.9rem;
      color: #6c757d;
    }
    .trainer-btn {
      border-top: 1px solid #ddd;
      text-align: center;
      padding: 0.75rem;
    }
  </style>
</head>
<body>

<main class="container py-5">
  <h2 class="text-center mb-4">Certified Trainers</h2>
  <div class="row">
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
        <div class="card trainer-card">
          <img src="<?= htmlspecialchars($row['image']) ?>" alt="Trainer" class="trainer-img">
          <div class="trainer-info">
            <div class="trainer-name"><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></div>
            <div class="trainer-specialization"><?= htmlspecialchars($row['specialization']) ?></div>
            <div class="text-muted mt-1" style="font-size: 0.85rem;">
              <?= htmlspecialchars($row['city']) ?>, <?= htmlspecialchars($row['country']) ?>
            </div>
          </div>
          <div class="trainer-btn">
            <a href="trainer_view.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary btn-sm">
              <i class="bi bi-eye"></i> View Profile
            </a>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</main>

<?php $conn->close(); ?>
<?php include 'includes/footer.php'; ?>

</body>
</html>
