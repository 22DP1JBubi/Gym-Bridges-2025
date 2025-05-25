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
$target = $_GET['target'] ?? null;
$search = $_GET['search'] ?? null;
$alphaSort = $_GET['alpha_sort'] ?? null;

$where = [];
$params = [];
$types = '';

if ($goal) { $where[] = "goal = ?"; $params[] = $goal; $types .= 's'; }
if ($type) { $where[] = "type = ?"; $params[] = $type; $types .= 's'; }
if ($level) { $where[] = "level = ?"; $params[] = $level; $types .= 's'; }
if ($target) { $where[] = "target_group = ?"; $params[] = $target; $types .= 's'; }
if ($search) { $where[] = "title LIKE ?"; $params[] = "%$search%"; $types .= 's'; }

$sql = "SELECT * FROM workout_programs";
if (!empty($where)) $sql .= " WHERE " . implode(" AND ", $where);
if ($alphaSort === 'asc') $sql .= " ORDER BY title ASC";
elseif ($alphaSort === 'desc') $sql .= " ORDER BY title DESC";
else $sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$goals = ["Fat Loss", "Muscle Gain", "Strength Building", "Toning", "Endurance Improvement"];
$typesList = ["Split", "Full Body", "Upper/Lower"];
$levels = ["Beginner", "Intermediate", "Advanced"];
$targets = ["Men", "Women", "Seniors", "Athletes"];

function buildQuery($newParams = []) {
    $params = $_GET;
    foreach ($newParams as $key => $value) {
        if ($value === null) {
            unset($params[$key]);
        } else {
            $params[$key] = $value;
        }
    }
    return '?' . http_build_query($params);
}

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

  <form id="filtersForm" method="get" class="d-flex flex-wrap justify-content-center gap-2 mb-4">

  <!-- Поиск -->
  <div class="input-group" style="max-width: 400px;">
    <span class="input-group-text bg-white border-primary text-primary">
      <i class="bi bi-search"></i>
    </span>
    <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" class="form-control border-primary" placeholder="Search..." id="searchInput">
  </div>

  <!-- Goal -->
<div class="dropdown">
  <button class="btn <?= $goal ? 'btn-primary active' : 'btn-outline-primary' ?> dropdown-toggle" data-bs-toggle="dropdown">
    Goal
  </button>
  <ul class="dropdown-menu">
    <li><a class="dropdown-item <?= !$goal ? 'active' : '' ?>" href="<?= $_SERVER['PHP_SELF'] ?>">Show All</a></li>
    <?php foreach ($goals as $g): ?>
      <li>
        <a class="dropdown-item <?= ($goal === $g) ? 'active' : '' ?>"
           href="?<?= http_build_query(array_merge($_GET, ['goal' => $g])) ?>">
          <?= $g ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>

<!-- Type -->
<div class="dropdown">
  <button class="btn <?= $type ? 'btn-primary active' : 'btn-outline-primary' ?> dropdown-toggle" data-bs-toggle="dropdown">
    Type
  </button>
  <ul class="dropdown-menu">
    <li><a class="dropdown-item <?= !$type ? 'active' : '' ?>" href="<?= $_SERVER['PHP_SELF'] ?>">Show All</a></li>
    <?php foreach ($typesList as $t): ?>
      <li>
        <a class="dropdown-item <?= ($type === $t) ? 'active' : '' ?>"
           href="?<?= http_build_query(array_merge($_GET, ['type' => $t])) ?>">
          <?= $t ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>

<!-- Level -->
<div class="dropdown">
  <button class="btn <?= $level ? 'btn-primary active' : 'btn-outline-primary' ?> dropdown-toggle" data-bs-toggle="dropdown">
    Level
  </button>
  <ul class="dropdown-menu">
    <li><a class="dropdown-item <?= !$level ? 'active' : '' ?>" href="<?= $_SERVER['PHP_SELF'] ?>">Show All</a></li>
    <?php foreach ($levels as $l): ?>
      <li>
        <a class="dropdown-item <?= ($level === $l) ? 'active' : '' ?>"
           href="?<?= http_build_query(array_merge($_GET, ['level' => $l])) ?>">
          <?= $l ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>

<!-- Target Group -->
<div class="dropdown">
  <button class="btn <?= $target ? 'btn-primary active' : 'btn-outline-primary' ?> dropdown-toggle" data-bs-toggle="dropdown">
    Target Group
  </button>
  <ul class="dropdown-menu">
    <li><a class="dropdown-item <?= !$target ? 'active' : '' ?>" href="<?= $_SERVER['PHP_SELF'] ?>">Show All</a></li>
    <?php foreach ($targets as $tg): ?>
      <li>
        <a class="dropdown-item <?= ($target === $tg) ? 'active' : '' ?>"
           href="?<?= http_build_query(array_merge($_GET, ['target_group' => $tg])) ?>">
          <?= $tg ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>

  <!-- Сортировка A-Z / Z-A -->
  <div class="btn-group">
    <a href="<?= buildQuery(['alpha_sort' => 'asc']) ?>" class="btn btn-outline-primary <?= ($alphaSort === 'asc') ? 'active' : '' ?>">A–Z</a>
    <a href="<?= buildQuery(['alpha_sort' => 'desc']) ?>" class="btn btn-outline-primary <?= ($alphaSort === 'desc') ? 'active' : '' ?>">Z–A</a>
  </div>
</form>



  <div class="row">
    <?php if ($result->num_rows > 0): ?>
      <?php while ($prog = $result->fetch_assoc()): ?>
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="card">
            <div class="card-img" style="height:220px;background-size:cover;background-image:url('<?= htmlspecialchars($prog['image']) ?>')">
              <div class="overlay d-flex align-items-center justify-content-center" style="background:rgba(0,0,0,0.5);opacity:0;transition:0.3s;">
                <a href="workout_program_view.php?id=<?= $prog['id'] ?>" class="btn btn-light">Show program</a>
              </div>
            </div>
            <div class="card-content p-3">
              <h5><?= htmlspecialchars($prog['title']) ?></h5>
              <p><strong>Goal:</strong> <?= htmlspecialchars($prog['goal']) ?></p>
              <p><strong>Type:</strong> <?= htmlspecialchars($prog['type']) ?></p>
              <p class="text-muted small"><?= htmlspecialchars(mb_strimwidth($prog['description'], 0, 70, "...")) ?></p>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="col-12 text-center mt-4">
        <p class="text-muted">No workout programs found for the selected filters.</p>
      </div>
    <?php endif; ?>
  </div>
</div>


</div>

<?php $stmt->close(); $conn->close(); ?>
<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const input = document.getElementById('searchInput');
  let timeout = null;

  input.addEventListener('input', function () {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
      document.getElementById('filtersForm').submit();
    }, 700);
  });
});
</script>


</body>
</html>
