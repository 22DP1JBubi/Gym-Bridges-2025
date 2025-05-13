<?php
session_start();

$conn = new mysqli("localhost", "root", "", "gymbridges");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
include 'includes/avatar_loader.php';

mysqli_set_charset($conn, "utf8mb4");

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("SELECT * FROM exercises WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$exercise = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$exercise) {
    echo "Exercise not found.";
    exit;
}



function getYouTubeEmbedUrl($url) {
    if (preg_match('/watch\?v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
        return 'https://www.youtube.com/embed/' . $matches[1];
    } elseif (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
        return 'https://www.youtube.com/embed/' . $matches[1];
    } elseif (strpos($url, 'embed') !== false) {
        return $url; // уже embed-ссылка
    }
    return null;
}

$embedUrl = getYouTubeEmbedUrl($exercise['video_url']);


$muscleGroups = explode(',', $exercise['muscle_group']);
$muscleGroups = array_map('trim', $muscleGroups); // убрать пробелы

$mainMuscle = $muscleGroups[0] ?? null;
$secondaryMuscles = array_slice($muscleGroups, 1);


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($exercise['name']) ?> - Gym Bridges</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href='https://fonts.googleapis.com/css?family=Anton' rel='stylesheet'>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
  <style>
    .badge:hover {
        filter: brightness(90%);
        transition: 0.2s ease-in-out;
        cursor: pointer;
    }

  </style>
</head>
<body class="bg-light">

<?php include 'includes/header.php'; ?>

<div class="container my-5">
  <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">

    <h1 class="text-center fw-bold mb-4 display-5"><?= htmlspecialchars($exercise['name']) ?></h1>

    <div class="row g-4">
      <?php if ($exercise['image']): ?>
        <div class="col-md-6">
          <img src="<?= htmlspecialchars($exercise['image']) ?>" class="img-fluid rounded-4 shadow-sm" alt="<?= htmlspecialchars($exercise['name']) ?>">
        </div>
      <?php endif; ?>

      <div class="col-md-6 d-flex flex-column justify-content-between">
        <div class="bg-light rounded-3 p-3 mb-3">
          <h5 class="fw-bold mb-2"><i class="bi bi-info-circle me-2"></i>Description</h5>
          <p class="mb-0"><?= nl2br(htmlspecialchars($exercise['description'])) ?></p>
        </div>

        <?php if (!empty($exercise['instruction'])): ?>
          <div class="bg-light rounded-3 p-3">
            <h5 class="fw-bold mb-2"><i class="bi bi-list-check me-2"></i>Instruction</h5>
            <ol class="ps-3 mb-0">
              <?php foreach (explode("\n", trim($exercise['instruction'])) as $line): ?>
                <?php if (trim($line) !== ''): ?>
                  <li><?= htmlspecialchars($line) ?></li>
                <?php endif; ?>
              <?php endforeach; ?>
            </ol>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- BADGES -->
    <div class="row mt-4">
        <div class="col d-flex flex-wrap gap-4 justify-content-center border-top pt-4">

            <?php if ($exercise['category']): ?>
                <div>
                    <small class="text-muted d-block"><i class="bi bi-tag"></i> Category</small>
                    <?php foreach (explode(',', $exercise['category']) as $c): ?>
                    <?php $cTrim = ucfirst(strtolower(trim($c))); ?>
                    <a href="exercises_page.php?category=<?= urlencode($cTrim) ?>" class="badge bg-primary text-decoration-none"><?= htmlspecialchars($cTrim) ?></a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>


            <?php if ($mainMuscle): ?>
                <div>
                    <small class="text-muted d-block text-center">
                    <i class="bi bi-bullseye"></i> Main muscle
                    </small>
                    <a href="exercises_page.php?muscle=<?= urlencode($mainMuscle) ?>" class="badge bg-success text-decoration-none">
                    <?= htmlspecialchars($mainMuscle) ?>
                    </a>
                </div>
            <?php endif; ?>


            <?php if (!empty($secondaryMuscles)): ?>
                <div>
                    <small class="text-muted d-block text-center">
                    <i class="bi bi-plus-square-dotted"></i> Secondary muscles
                    </small>
                    <?php foreach ($secondaryMuscles as $m): ?>
                    <a href="exercises_page.php?muscle=<?= urlencode($m) ?>" class="badge bg-info text-decoration-none me-1">
                        <?= htmlspecialchars($m) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>


            <?php if ($exercise['equipment']): ?>
            <div class="text-center">
                <div class="text-secondary small mb-1"><i class="bi bi-tools me-1"></i>Equipment</div>
                <?php foreach (explode(',', $exercise['equipment']) as $e): ?>
                <span class="badge bg-secondary"><?= htmlspecialchars(trim($e)) ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="text-center">
            <div class="text-secondary small mb-1"><i class="bi bi-bar-chart-line me-1"></i>Difficulty</div>
            <?php
                $diff = intval($exercise['difficulty']);
                for ($i = 1; $i <= 5; $i++) {
                $icon = $i <= $diff ? 'images/icon-dumbbell-color2.png' : 'images/icon-dumbbell-black2.png';
                echo '<img src="' . $icon . '" alt="dumbbell" width="24" height="24" class="me-1">';
                }
            ?>
            </div>

        </div>
    </div>


    <!-- VIDEO -->

    <?php if ($embedUrl): ?>
      <div class="row mt-5">
        <div class="col-lg-12">
          <div class="bg-light p-4 rounded-4 shadow-sm">
            <h5 class="text-center fw-bold mb-3"><i class="bi bi-play-circle me-2"></i>Watch the Exercise in Action</h5>
            <div class="ratio ratio-16x9 rounded overflow-hidden">
              <iframe src="<?= htmlspecialchars($embedUrl) ?>" title="Exercise Video" allowfullscreen></iframe>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>


  </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'includes/footer.php'; ?>

</body>
</html>
