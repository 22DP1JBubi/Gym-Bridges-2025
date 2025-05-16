<?php
session_start();

$conn = new mysqli("localhost", "root", "", "gymbridges");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
include 'includes/avatar_loader.php';

mysqli_set_charset($conn, "utf8mb4");

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $conn->prepare("SELECT * FROM workout_programs WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$program = $result->fetch_assoc();
$stmt->close();

if (!$program) {
    echo "Program not found.";
    exit;
}


// Предварительно получаем названия всех упражнений в массив:
$exerciseNames = [];
$res = $conn->query("SELECT id, name FROM exercises");
while ($row = $res->fetch_assoc()) {
    $exerciseNames[$row['id']] = $row['name'];
}





$days = json_decode($program['days_json'], true);


$image = $program['image'];


$mainMuscles = array_map('trim', explode(',', $program['muscle_groups']));
$muscleCategories = array_map('trim', explode(',', $program['muscle_categories']));

include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($program['title']) ?> - Gym Bridges</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href='https://fonts.googleapis.com/css?family=Anton' rel='stylesheet'>
  <link rel="stylesheet" type="text/css" href="style.css">

  <style>
  .link-animated {
    text-decoration: none;
    color: #212529;
    position: relative;
    transition: color 0.2s ease-in-out;
  }

  .link-animated::after {
    content: '';
    position: absolute;
    width: 0;
    height: 2px;
    bottom: -2px;
    left: 0;
    background-color: #0d6efd; /* Bootstrap primary */
    transition: width 0.25s ease-in-out;
  }

  .link-animated:hover {
    color: #0d6efd;
  }

  .link-animated:hover::after {
    width: 100%;
  }

  .modern-description {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-left: 5px solid #457B9D;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
    transition: background 0.3s ease;
  }

  .modern-description:hover {
    background: linear-gradient(135deg, #f1f3f5, #dee2e6);
  }

  .modern-description h5 {
    font-size: 1.4rem;
    color: #457B9D;
    display: flex;
    align-items: center;
  }

  .modern-description h5 i {
    font-size: 1.6rem;
    margin-right: 0.5rem;
  }

  .modern-description p {
    font-size: 1.05rem;
    line-height: 1.6;
    color: #343a40;
    margin-top: 0.5rem;
  }


</style>
</head>
<body class="bg-light">

<div class="container my-5">
  <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
    <h1 class="text-center fw-bold mb-4 display-5"><?= htmlspecialchars($program['title']) ?></h1>

    <div class="row g-4">
      <!-- Картинка и инфо-блок слева -->
      <div class="col-md-6 d-flex flex-column align-items-center">
        <?php if ($image): ?>
          <img src="<?= htmlspecialchars($image) ?>" class="img-fluid rounded-4 shadow-sm mb-3" alt="Program image" style="max-height: 400px;">
        <?php endif; ?>

        <!-- Информационные бейджи -->
        <div class="text-start border rounded p-3 bg-light mt-3 w-100">
          <div class="mb-3">
            <i class="bi bi-hourglass-split text-secondary me-1"></i>
            <span class="text-muted">Duration of the workout program:</span>
            <span class="badge bg-success text-white"><?= htmlspecialchars($program['duration_weeks']) ?> weeks</span>
          </div>

          <div class="mb-3">
            <i class="bi bi-calendar-week text-secondary me-1"></i>
            <span class="text-muted">Training days per week:</span>
            <span class="badge bg-dark text-white"><?= htmlspecialchars($program['days_per_week']) ?> days</span>
          </div>

          <?php if (!empty($muscleCategories)): ?>
            <div class="mb-3">
              <i class="bi bi-tags-fill text-secondary me-1"></i>
              <span class="text-muted">Muscle category:</span>
              <span class="fw-semibold">
                <?php foreach ($muscleCategories as $i => $cat): ?>
                  <a href="exercises_page.php?category=<?= urlencode(ucfirst($cat)) ?>" class="link-animated">
                    <?= htmlspecialchars(ucfirst($cat)) ?>
                  </a><?= $i < count($muscleCategories) - 1 ? ' → ' : '' ?>
                <?php endforeach; ?>
              </span>
            </div>
          <?php endif; ?>

          <?php if (!empty($mainMuscles)): ?>
            <div>
              <i class="bi bi-bullseye text-secondary me-1"></i>
              <span class="text-muted">Main muscles involved:</span>
              <span class="fw-semibold">
                <?php foreach ($mainMuscles as $i => $muscle): ?>
                  <a href="exercises_page.php?muscle=<?= urlencode($muscle) ?>" class="link-animated">
                    <?= htmlspecialchars($muscle) ?>
                  </a><?= $i < count($mainMuscles) - 1 ? ' → ' : '' ?>
                <?php endforeach; ?>
              </span>
            </div>
          <?php endif; ?>



        </div>
      </div>

      <!-- Описание справа -->
      <div class="col-md-6">
        <?php if ($program['description']): ?>
          <div class="modern-description p-4 rounded-3 h-100">
            <h5><i class="bi bi-info-circle-fill"></i>Description</h5>
            <p><?= nl2br(htmlspecialchars($program['description'])) ?></p>
          </div>
        <?php endif; ?>
      </div>

    </div>



    <hr class="my-4">

    <div class="row text-center small mb-3">
      <div class="col d-flex flex-wrap justify-content-center gap-3">
        <div><i class="bi bi-bullseye me-1"></i><strong>Goal:</strong> <?= htmlspecialchars($program['goal']) ?></div>
        <div><i class="bi bi-diagram-3 me-1"></i><strong>Type:</strong> <?= htmlspecialchars($program['type']) ?></div>
        <div><i class="bi bi-person-badge me-1"></i><strong>Level:</strong> <?= htmlspecialchars($program['level']) ?></div>
        <div><i class="bi bi-people me-1"></i><strong>Target:</strong> <?= htmlspecialchars($program['target_group']) ?></div>
      </div>
    </div>

    <hr class="my-4">


    <!-- Coach Tips отдельным блоком ниже -->
    <?php if ($program['tips']): ?>
        <div class="modern-description p-4 rounded-3 h-100">
          <h5><i class="bi bi-lightbulb"></i>Coach's Tips</h5>
          <p><?= nl2br(htmlspecialchars($program['tips'])) ?></p>
        </div>
      <?php endif; ?>


    <?php if ($days && is_array($days)): ?>
      <h3 class="text-center my-4"><i class="bi bi-calendar3 me-2"></i>Workout Plan</h3>

      <?php foreach ($days as $day): ?>
        <div class="card mb-4 shadow-sm border-0">
          <div class="card-header bg-primary text-white fw-bold fs-5">
            <?= htmlspecialchars($day['title']) ?>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Exercise</th>
                    <th style="width: 20%;">Sets</th>
                    <th style="width: 20%;">Reps</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($day['exercises'] as $ex): ?>
                    <?php
                      $exerciseId = $ex['exercise_id'];
                      $exerciseName = $exerciseNames[$exerciseId] ?? 'Unknown Exercise';
                    ?>
                    <tr>
                      <td>
                        <a href="exercise_view.php?id=<?= $exerciseId ?>" class="link-animated">
                          <?= htmlspecialchars($exerciseName) ?>
                        </a>
                      </td>
                      <td><?= htmlspecialchars($ex['sets']) ?></td>
                      <td><?= htmlspecialchars($ex['reps']) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>


  </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'includes/footer.php'; ?>
</body>
</html>
