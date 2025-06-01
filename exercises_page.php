<?php
session_start();

$conn = new mysqli("localhost", "root", "", "gymbridges");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
include 'includes/avatar_loader.php';

mysqli_set_charset($conn, "utf8mb4");

$muscleGroups = [
  "Arms" => ["Biceps", "Triceps", "Shoulders", "Forearms"],
  "Chest" => ["Chest Muscles"],
  "Abs" => ["Abdominal Muscles"],
  "Back" => ["Lats", "Middle/Lower Trapezius", "Teres major", "Lower Back", "Upper Trapezius"],
  "Legs" => ["Quadriceps", "Hamstrings", "Adductors", "Calves", "Glutes"]
];

$muscle = $_GET['muscle'] ?? null;
$category = $_GET['category'] ?? null;
$diffSort = $_GET['diff_sort'] ?? '';
$alphaSort = $_GET['alpha_sort'] ?? '';
$difficulty = $_GET['difficulty'] ?? [];
$search = $_GET['search'] ?? null;

$perPage = 16;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// Определение активной категории
$currentMuscle = $muscle;
$currentCategory = $category;
$activeCategory = null;
if (!$currentCategory && $currentMuscle) {
    foreach ($muscleGroups as $cat => $muscles) {
        if (in_array($currentMuscle, $muscles)) {
            $currentCategory = $cat;
            $activeCategory = $cat;
            break;
        }
    }
} elseif ($currentCategory && isset($muscleGroups[$currentCategory])) {
    $activeCategory = $currentCategory;
}

// Подготовка запроса
$whereParts = [];
$params = [];
$types = '';

if ($muscle) {
    $whereParts[] = "FIND_IN_SET(?, muscle_group)";
    $params[] = $muscle;
    $types .= 's';
} elseif ($category && isset($muscleGroups[$category])) {
    $group = $muscleGroups[$category];
    $groupConditions = array_fill(0, count($group), "FIND_IN_SET(?, muscle_group)");
    $whereParts[] = '(' . implode(' OR ', $groupConditions) . ')';
    $params = array_merge($params, $group);
    $types .= str_repeat('s', count($group));
}

if (!empty($difficulty) && is_array($difficulty)) {
    $valid = array_filter($difficulty, fn($v) => in_array($v, ['1','2','3','4','5']));
    if ($valid) {
        $placeholders = implode(',', array_fill(0, count($valid), '?'));
        $whereParts[] = "difficulty IN ($placeholders)";
        $params = array_merge($params, $valid);
        $types .= str_repeat('i', count($valid));
    }
}

if ($search) {
    $whereParts[] = "name LIKE ?";
    $params[] = '%' . $search . '%';
    $types .= 's';
}

// === Считаем общее количество
$countQuery = "SELECT COUNT(*) as total FROM exercises";
if (!empty($whereParts)) {
    $countQuery .= " WHERE " . implode(" AND ", $whereParts);
}
$countStmt = $conn->prepare($countQuery);
if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalResult = $countStmt->get_result();
$totalExercises = $totalResult->fetch_assoc()['total'] ?? 0;
$countStmt->close();
$totalPages = ceil($totalExercises / $perPage);

// === Запрос данных
$baseQuery = "SELECT * FROM exercises";
if (!empty($whereParts)) {
    $baseQuery .= " WHERE " . implode(" AND ", $whereParts);
}

// === Сортировка ===
$orderClause = '';
if ($alphaSort === 'asc') {
    $orderClause = " ORDER BY name ASC";
} elseif ($alphaSort === 'desc') {
    $orderClause = " ORDER BY name DESC";
} elseif ($diffSort === 'asc') {
    $orderClause = " ORDER BY difficulty ASC";
} elseif ($diffSort === 'desc') {
    $orderClause = " ORDER BY difficulty DESC";
}

$baseQuery .= $orderClause;
$baseQuery .= " LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;
$types .= 'ii';


// Выполнение
$stmt = $conn->prepare($baseQuery);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gym bridges</title>
  <!-- Подключение CSS файла -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link href='https://fonts.googleapis.com/css?family=Anton' rel='stylesheet'>
  <link rel="icon" href="Logo1.svg" type="image/x-icon">
  <link rel="stylesheet" type="text/css" href="style.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  <script type="text/javascript" src="jquery-1.6.4.min.js"></script>
  <script type="text/javascript" src="jquery.maphilight.js"></script>
  <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCaLxpPOdyV8bVweF1y0AQRAtLPdfftFvs&callback=initMap"></script>
  <style>
    .muscle-text {
      color: #0D1C2E;
      font-family: Anton;
      font-size: 36px;
      font-style: normal;
      font-weight: 400;
      line-height: normal;
      letter-spacing: 1.8px;
      text-align: center;
    }
    .muscle-text2 {
      color: #0D1C2E;
      font-family: Anton;
      font-size: 36px;
      font-style: normal;
      font-weight: 400;
      line-height: normal;
      letter-spacing: 1.8px;
    }
    .row.justify-content-center {
      margin-top: 100px;
      margin-bottom: 100px;
    }
    .card {
      border-radius: 4px;
      box-shadow: 0 -1px 1px 0 rgba(0, 0, 0, .05), 0 1px 2px 0 rgba(0, 0, 0, .2);
      transition: all .2s ease;
      background: #fff;
      position: relative;
      overflow: hidden;
    }
    .card:hover, .card.hover {
      transform: translateY(-4px);
      box-shadow: 0 4px 25px 0 rgba(0, 0, 0, .3), 0 0 1px 0 rgba(0, 0, 0, .25);
    }
    .card:hover .card-content, .card.hover .card-content {
      box-shadow: inset 0 3px 0 0 #ccb65e;
      border-color: #ccb65e;
    }
    .card:hover .card-img .overlay, .card.hover .card-img .overlay {
      background-color: rgba(25, 29, 38, .85);
      transition: opacity .2s ease;
      opacity: 1;
    }
    .card-img {
      position: relative;
      height: 224px;
      width: 100%;
      background-color: #fff;
      transition: opacity .2s ease;
      background-position: center center;
      background-repeat: no-repeat;
      background-size: cover;
    }
    .card-img .overlay {
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: #fff;
      opacity: 0;
    }
    .card-img .overlay .overlay-content {
      line-height: 224px;
      width: 100%;
      text-align: center;
      color: #fff;
    }
    .card-img .overlay .overlay-content a {
      color: #fff;
      padding: 0 2rem;
      display: inline-block;
      border: 1px solid rgba(255, 255, 255, .4);
      height: 40px;
      line-height: 40px;
      border-radius: 20px;
      cursor: pointer;
      text-decoration: none;
    }
    .card-img .overlay .overlay-content a:hover, .card-img .overlay .overlay-content a.hover {
      background-color: #ccb65e;
      border-color: #ccb65e;
    }
    .card-content {
      width: 100%;
      min-height: 136px;
      background-color: #fff;
      border-top: 1px solid #E9E9EB;
      border-bottom-right-radius: 4px;
      border-bottom-left-radius: 4px;
      padding: 1rem 2rem;
      transition: all .2s ease;
    }
    .card-content a {
      text-decoration: none;
      color: #202927;
    }
    .card-content h2, .card-content a h2 {
      font-size: 1rem;
      font-weight: 500;
    }
    .card-content p, .card-content a p {
      font-size: .8rem;
      font-weight: 400;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      color: rgba(32, 41, 28, .8);
    }


    .dropdown-submenu ul {
      padding-left: 0;
      margin-bottom: 0;
    }
    .dropdown-submenu span {
      display: block;
      padding: .25rem 1rem;
      color: #6c757d;
    }
    .dropdown-submenu a.dropdown-item {
      padding-left: 2rem;
    }


  </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main>
<div class="container">
  <div class="row justify-content-center">
    <?php if ($muscle): ?>
    <h2><?= htmlspecialchars($muscle) ?> Exercises</h2>
  <?php elseif ($category): ?>
    <h2><?= htmlspecialchars($category) ?> Exercises</h2>
  <?php else: ?>
    <h2>All Exercises</h2>
  <?php endif; ?>





    <div class="d-flex flex-wrap gap-2 justify-content-center mb-4">
      <?php
      $alphaSort = $_GET['alpha_sort'] ?? '';
      $diffSort = $_GET['diff_sort'] ?? '';
      $difficulty = $_GET['difficulty'] ?? [];

      $baseLink = "exercises_page.php?";

      $searchParam = $search ? "search=" . urlencode($search) . "&" : "";
      $diffSortParam = $diffSort ? "diff_sort=" . urlencode($diffSort) . "&" : "";


      if ($currentCategory) $baseLink .= "category=" . urlencode($currentCategory) . "&";
      if ($currentMuscle) $baseLink .= "muscle=" . urlencode($currentMuscle) . "&";

      $difficultyParams = '';
      foreach ((array)$difficulty as $d) {
          $difficultyParams .= "difficulty[]=" . urlencode($d) . "&";
      }
      ?>

      <!-- Поиск -->
      <form method="get" id="searchForm" class="d-flex">
        <?php if ($currentCategory): ?>
          <input type="hidden" name="category" value="<?= htmlspecialchars($currentCategory) ?>">
        <?php endif; ?>
        <?php if ($currentMuscle): ?>
          <input type="hidden" name="muscle" value="<?= htmlspecialchars($currentMuscle) ?>">
        <?php endif; ?>
        <?php foreach ((array)$difficulty as $d): ?>
          <input type="hidden" name="difficulty[]" value="<?= htmlspecialchars($d) ?>">
        <?php endforeach; ?>
        <?php if ($diffSort): ?>
          <input type="hidden" name="diff_sort" value="<?= htmlspecialchars($diffSort) ?>">
        <?php endif; ?>
        <?php if ($alphaSort): ?>
          <input type="hidden" name="alpha_sort" value="<?= htmlspecialchars($alphaSort) ?>">
        <?php endif; ?>
         <div class="input-group" style="min-width: 400px; ">
          <span class="input-group-text bg-white border-primary text-primary">
            <i class="bi bi-search"></i>
          </span>
          <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                class="form-control border-primary" placeholder="Search..." id="searchInput">
        </div>
      </form>




      <!-- Кнопка Show All -->
      <a href="exercises_page.php" class="btn btn-outline-primary <?= (!$currentMuscle && !$currentCategory) ? 'active' : '' ?>">Show All</a>

      <!-- Категории -->
      <?php foreach ($muscleGroups as $category => $muscles): ?>
        <div class="dropdown">
          <button class="btn btn-outline-primary dropdown-toggle <?= ($currentCategory === $category || in_array($currentMuscle, $muscles)) ? 'active' : '' ?>" type="button" data-bs-toggle="dropdown">
            <?= htmlspecialchars($category) ?>
          </button>
          <ul class="dropdown-menu">
            <!-- All in category -->
            <li>
              <a class="dropdown-item <?= ($currentCategory === $category && !$currentMuscle) ? 'active' : '' ?>"
                href="exercises_page.php?category=<?= urlencode($category) ?>">
                All <?= htmlspecialchars($category) ?> exercises
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <!-- Отдельные мышцы -->
            <?php foreach ($muscles as $muscle): ?>
              <li>
                <a class="dropdown-item <?= ($currentMuscle === $muscle) ? 'active' : '' ?>"
                  href="exercises_page.php?muscle=<?= urlencode($muscle) ?>">
                  <?= htmlspecialchars($muscle) ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endforeach; ?>

    </div>

      
    <div class="d-flex flex-wrap gap-2 justify-content-center mb-4">
  <!-- A-Z сортировка -->

    
    <!-- A-Z / Z-A -->
    <div class="btn-group">
       <a href="<?= $baseLink . $searchParam . $diffSortParam ?>alpha_sort=asc&<?= $difficultyParams ?>" class="btn btn-outline-primary <?= ($alphaSort === 'asc') ? 'active' : '' ?>">A–Z</a>
       <a href="<?= $baseLink . $searchParam . $diffSortParam ?>alpha_sort=desc&<?= $difficultyParams ?>" class="btn btn-outline-primary <?= ($alphaSort === 'desc') ? 'active' : '' ?>">Z–A</a>
    </div>

    <!-- Difficulty фильтр и сортировка -->
    <div class="dropdown">
      <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
        <i class="bi bi-sliders me-1"></i> Difficulty
      </button>

      <form class="dropdown-menu p-3" style="min-width: 250px;" method="get" id="difficultyForm">
        <!-- Hidden for muscle/category context -->
         <?php if ($search): ?>
          <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
        <?php endif; ?>

        <?php if ($currentCategory): ?>
          <input type="hidden" name="category" value="<?= htmlspecialchars($currentCategory) ?>">
        <?php endif; ?>
        <?php if ($currentMuscle): ?>
          <input type="hidden" name="muscle" value="<?= htmlspecialchars($currentMuscle) ?>">
        <?php endif; ?>
        <label class="form-check">
          <input class="form-check-input" type="checkbox" name="difficulty[]" value="1" <?= in_array("1", $difficulty) ? "checked" : "" ?>>
          <span>Difficulty:
            <?= str_repeat('<img src="images/icon-dumbbell-color2.png" width="16">', 1) ?>
          </span>
        </label>

        <label class="form-check">
          <input class="form-check-input" type="checkbox" name="difficulty[]" value="2" <?= in_array("2", $difficulty) ? "checked" : "" ?>>
          <span>Difficulty:
            <?= str_repeat('<img src="images/icon-dumbbell-color2.png" width="16">', 2) ?>
          </span>
        </label>

        <label class="form-check">
          <input class="form-check-input" type="checkbox" name="difficulty[]" value="3" <?= in_array("3", $difficulty) ? "checked" : "" ?>>
          <span>Difficulty:
            <?= str_repeat('<img src="images/icon-dumbbell-color2.png" width="16">', 3) ?>
          </span>
        </label>

        <label class="form-check">
          <input class="form-check-input" type="checkbox" name="difficulty[]" value="4" <?= in_array("4", $difficulty) ? "checked" : "" ?>>
          <span>Difficulty:
            <?= str_repeat('<img src="images/icon-dumbbell-color2.png" width="16">', 4) ?>
          </span>
        </label>

        <label class="form-check">
          <input class="form-check-input" type="checkbox" name="difficulty[]" value="5" <?= in_array("5", $difficulty) ? "checked" : "" ?>>
          <span>Difficulty:
            <?= str_repeat('<img src="images/icon-dumbbell-color2.png" width="16">', 5) ?>
          </span>
        </label>

        <hr class="my-2">

        <label class="form-check">
          <input class="form-check-input" type="radio" name="diff_sort" value="asc" <?= ($diffSort === 'asc') ? "checked" : "" ?>>
          Difficulty ↑
        </label>
        <label class="form-check">
          <input class="form-check-input" type="radio" name="diff_sort" value="desc" <?= ($diffSort === 'desc') ? "checked" : "" ?>>
          Difficulty ↓
        </label>

        <!-- auto submit, no button needed -->

      </form>
    </div>
  </div>




    <div class="row">
      <?php if ($result->num_rows === 0): ?>
        <div class="col-12 text-center mt-4">
          <p class="text-muted">No exercises found for the selected filters.</p>
        </div>
      <?php endif; ?>

      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="card">
            <div class="card-img" style="background-image:url('<?= htmlspecialchars($row['image']) ?>');">
              <div class="overlay">
                <div class="overlay-content">
                  <a class="hover" href="exercise_view.php?id=<?= $row['id'] ?>">Show exercise</a>
                </div>
              </div>
            </div>
            <div class="card-content">
              <a href="exercise_view.php?id=<?= $row['id'] ?>">
                <h2><?= htmlspecialchars($row['name']) ?></h2>
                <p><?= htmlspecialchars(mb_strimwidth($row['description'], 0, 60, "...")) ?></p>
              </a>

              <p class="mt-2 mb-0">
                <?php
                $diff = intval($row['difficulty']);
                for ($i = 1; $i <= 5; $i++) {
                    $icon = $i <= $diff ? 'images/icon-dumbbell-color2.png' : 'images/icon-dumbbell-black2.png';
                    echo '<img src="' . $icon . '" alt="dumbbell" width="20" height="20" class="me-1">';
                }
                ?>
              
            </div>
          </div>
        </div>
      <?php endwhile; ?>
      
      <?php if ($totalPages > 1): ?>
        <div class="d-flex justify-content-center mt-4">
          <nav>
            <ul class="pagination">

              <?php
              // Сохраняем текущие GET-параметры, кроме page
              $queryWithoutPage = $_GET;
              unset($queryWithoutPage['page']);
              ?>

              <?php if ($page > 1): ?>
                <li class="page-item">
                  <a class="page-link" href="?<?= http_build_query(array_merge($queryWithoutPage, ['page' => $page - 1])) ?>">Previous</a>
                </li>
              <?php endif; ?>

              <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                  <a class="page-link" href="?<?= http_build_query(array_merge($queryWithoutPage, ['page' => $i])) ?>"><?= $i ?></a>
                </li>
              <?php endfor; ?>

              <?php if ($page < $totalPages): ?>
                <li class="page-item">
                  <a class="page-link" href="?<?= http_build_query(array_merge($queryWithoutPage, ['page' => $page + 1])) ?>">Next</a>
                </li>
              <?php endif; ?>

            </ul>
          </nav>
        </div>
      <?php endif; ?>



    </div>
  </div>
</div>

<?php $stmt->close(); $conn->close(); ?>

</main>

<?php include 'includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('difficultyForm');
  if (form) {
    form.querySelectorAll('input').forEach(input => {
      input.addEventListener('change', () => {
        form.submit();
      });
    });
  }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const input = document.getElementById('searchInput');
  let timeout = null;

  input.addEventListener('input', function () {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
      document.getElementById('searchForm').submit();
    }, 1000); // 500 мс задержка
  });
});
</script>

</body>
</html>
