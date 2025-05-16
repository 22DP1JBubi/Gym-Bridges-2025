<?php
$conn = new mysqli("localhost", "root", "", "gymbridges");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
include 'includes/avatar_loader.php';

mysqli_set_charset($conn, "utf8mb4");

$goal = $_GET['goal'] ?? null;
$type = $_GET['type'] ?? null;
$level = $_GET['level'] ?? null;

$where = [];
$params = [];
$types = '';

if ($goal) { $where[] = "goal = ?"; $params[] = $goal; $types .= 's'; }
if ($type) { $where[] = "type = ?"; $params[] = $type; $types .= 's'; }
if ($level) { $where[] = "level = ?"; $params[] = $level; $types .= 's'; }

$sql = "SELECT * FROM workout_programs";
if (!empty($where)) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Workout Programs</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link href='https://fonts.googleapis.com/css?family=Anton' rel='stylesheet'>
  <link rel="stylesheet" type="text/css" href="style.css">
  <style>
    body {
      background: #f9f9f9;
    }
    .card {
      border-radius: 4px;
      box-shadow: 0 -1px 1px 0 rgba(0, 0, 0, .05), 0 1px 2px 0 rgba(0, 0, 0, .2);
      transition: all .2s ease;
      background: #fff;
      position: relative;
      overflow: hidden;
    }
    .card:hover {
      transform: translateY(-4px);
      box-shadow: 0 4px 25px 0 rgba(0, 0, 0, .3), 0 0 1px 0 rgba(0, 0, 0, .25);
    }
    .card-img {
      position: relative;
      height: 220px;
      width: 100%;
      background-color: #fff;
      background-size: cover;
      background-position: center;
    }
    .card-img .overlay {
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(25, 29, 38, .85);
      opacity: 0;
      transition: opacity .2s ease;
    }
    .card:hover .overlay {
      opacity: 1;
    }
    .card-img .overlay .overlay-content {
      line-height: 220px;
      width: 100%;
      text-align: center;
    }
    .card-img .overlay .overlay-content a {
      color: #fff;
      padding: 0 2rem;
      display: inline-block;
      border: 1px solid rgba(255, 255, 255, .4);
      height: 40px;
      line-height: 40px;
      border-radius: 20px;
      text-decoration: none;
    }
    .card-img .overlay .overlay-content a:hover {
      background-color: #ccb65e;
      border-color: #ccb65e;
    }
    .card-content {
      padding: 1rem 1.5rem;
      background-color: #fff;
      min-height: 190px;
    }
    .card-content h5 {
      font-size: 1rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }
    .card-content p {
      font-size: .9rem;
      color: #555;
      margin-bottom: 0.5rem;
    }
    .filters .btn.active {
      background-color: #0d6efd;
      color: white;
    }
  </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="container my-5">
  <h2 class="text-center mb-4">Workout Programs</h2>

  <!-- Фильтры -->
  <div class="filters d-flex flex-wrap gap-2 justify-content-center mb-4">
    <a href="?" class="btn btn-outline-primary <?= (!$goal && !$type && !$level) ? 'active' : '' ?>">All</a>
    <?php
      $goals = ["Fat Loss", "Muscle Gain", "Strength Building", "Toning", "Endurance Improvement"];
      foreach ($goals as $g) {
          echo '<a href="?goal=' . urlencode($g) . '" class="btn btn-outline-primary ' . ($goal === $g ? 'active' : '') . '">' . $g . '</a>';
      }
    ?>
  </div>

  <div class="row">
    <?php while ($prog = $result->fetch_assoc()): ?>
      <div class="col-lg-3 col-md-6 mb-4">
        <div class="card">
          <div class="card-img" style="background-image:url('<?= htmlspecialchars($prog['image']) ?>');">
            <div class="overlay">
              <div class="overlay-content">
                <a href="workout_program_view.php?id=<?= $prog['id'] ?>">Show program</a>
              </div>
            </div>
          </div>
          <div class="card-content">
            <h5><?= htmlspecialchars($prog['title']) ?></h5>
            <p><strong>Goal:</strong> <?= htmlspecialchars($prog['goal']) ?></p>
            <p><strong>Type:</strong> <?= htmlspecialchars($prog['type']) ?></p>
            <p class="text-muted small"><?= htmlspecialchars(mb_strimwidth($prog['description'], 0, 70, "...")) ?></p>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>

<?php $stmt->close(); $conn->close(); ?>
<?php include 'includes/footer.php'; ?>

</body>
</html>
