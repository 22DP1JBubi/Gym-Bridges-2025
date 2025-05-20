<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$conn = new mysqli("localhost", "root", "", "gymbridges");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
mysqli_set_charset($conn, "utf8mb4");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid ID");
}

$entry_id = intval($_GET['id']);

// Получаем текущую запись
$res = $conn->query("SELECT * FROM training_diary WHERE id = $entry_id AND user_id = $user_id LIMIT 1");
if ($res->num_rows === 0) {
    die("Entry not found or access denied.");
}
$entry = $res->fetch_assoc();

// Получаем упражнения
$exercises_result = $conn->query("SELECT * FROM training_diary_exercises WHERE diary_id = $entry_id");
$entry_exercises = [];
while ($row = $exercises_result->fetch_assoc()) {
    $row['sets_json'] = json_decode($row['sets_json'], true);
    $entry_exercises[] = $row;
}

// Все упражнения и программы
$programs = $conn->query("SELECT id, title FROM workout_programs ORDER BY title ASC");
$exercises = $conn->query("SELECT id, name FROM exercises ORDER BY name ASC");

// Обработка сохранения изменений
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_diary'])) {
    $date = $_POST['training_date'] ?? date('Y-m-d');
    $mode = $_POST['mode'] ?? 'custom';
    $program_id = ($mode === 'program' && !empty($_POST['program_id'])) ? intval($_POST['program_id']) : null;
    $program_day = !empty($_POST['program_day']) ? $_POST['program_day'] : null;
    $notes = $_POST['notes'] ?? '';
    $mood = !empty($_POST['mood_level']) ? intval($_POST['mood_level']) : null;

    $hours = isset($_POST['duration_hours']) ? intval($_POST['duration_hours']) : 0;
    $minutes = isset($_POST['duration_minutes']) ? intval($_POST['duration_minutes']) : 0;
    if ($minutes > 59) $minutes = 59;
    if ($hours > 12) $hours = 12;
    $training_time = sprintf('%02d:%02d:00', $hours, $minutes);

    // Обработка изображений (перезапись)
    $images = [];
    if (!empty($_FILES['images']['name'][0])) {
        $uploadDir = 'uploads/diary_images/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        foreach ($_FILES['images']['name'] as $key => $name) {
            $tmp_name = $_FILES['images']['tmp_name'][$key];
            $target = $uploadDir . basename($name);
            if (move_uploaded_file($tmp_name, $target)) {
                $images[] = $target;
            }
        }
    } else {
        // Если не загружено новых, оставить старые
        $images = json_decode($entry['images'], true);
    }

    $img_json = json_encode($images);

    // Обновление записи
    $stmt = $conn->prepare("UPDATE training_diary SET training_date=?, mode=?, program_id=?, program_day=?, mood_level=?, notes=?, images=?, training_time=? WHERE id=? AND user_id=?");
    $stmt->bind_param("ssisssssii",
        $date,
        $mode,
        $program_id,
        $program_day,
        $mood,
        $notes,
        $img_json,
        $training_time,
        $entry_id,
        $user_id
    );
    $stmt->execute();
    $stmt->close();

    // Удалим старые упражнения и сохраним новые
    $conn->query("DELETE FROM training_diary_exercises WHERE diary_id = $entry_id");

    if (!empty($_POST['exercises'])) {
        foreach ($_POST['exercises'] as $ex) {
            $ex_id = intval($ex['id']);
            $weight = $ex['weight'] ?? '';
            $sets = $ex['sets'] ?? '';
            $reps = $ex['reps'] ?? '';
            $sets_json = isset($ex['sets_json']) ? json_encode($ex['sets_json']) : null;

            $stmt = $conn->prepare("INSERT INTO training_diary_exercises 
                (diary_id, exercise_id, weight, sets, reps, sets_json) 
                VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iissss", $entry_id, $ex_id, $weight, $sets, $reps, $sets_json);
            $stmt->execute();
            $stmt->close();
        }
    }

    header("Location: training_diary.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Training Diary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        .card {
            border-radius: 15px;
            background-color: #ffffff;
        }
        .image-preview {
            position: relative;
            width: 120px;
            height: 120px;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #dee2e6;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            }

            .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            }

            .image-preview .remove-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(0, 0, 0, 0.6);
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            font-size: 14px;
            line-height: 1;
            cursor: pointer;
            }

    </style>
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow p-4 mb-5">

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="update_diary" value="1">
    <input type="hidden" name="diary_id" value="<?= $entry['id'] ?>">

    <div class="row mb-3">
        <!-- Date -->
        <div class="col-md-6 mt-4">
            <label class="form-label fw-bold">Date</label>
            <div class="input-group">
                <span class="input-group-text bg-light" id="calendar-addon" style="cursor: pointer;">
                    <i class="bi bi-calendar-event"></i>
                </span>
                <input type="text" id="training_date" name="training_date" class="form-control" value="<?= htmlspecialchars($entry['training_date']) ?>" required aria-describedby="calendar-addon">
            </div>
        </div>

        <!-- Duration -->
        <div class="col-md-6 mt-4">
            <label class="form-label fw-bold"><i class="bi bi-stopwatch me-1"></i> Duration of Workout</label>
            <div class="d-flex flex-wrap gap-3">
                <?php
                $parts = explode(':', $entry['training_time']);
                $dur_hours = intval($parts[0] ?? 0);
                $dur_minutes = intval($parts[1] ?? 0);
                ?>
                <div class="input-group" style="max-width: 160px;">
                    <span class="input-group-text bg-light"><i class="bi bi-hourglass-top"></i></span>
                    <input type="number" name="duration_hours" class="form-control" min="0" max="12" value="<?= $dur_hours ?>" required>
                    <span class="input-group-text">hrs</span>
                </div>

                <div class="input-group" style="max-width: 160px;">
                    <span class="input-group-text bg-light"><i class="bi bi-clock"></i></span>
                    <input type="number" name="duration_minutes" class="form-control" min="0" max="59" value="<?= $dur_minutes ?>" required>
                    <span class="input-group-text">min</span>
                </div>
            </div>
        </div>

        <!-- Mood -->
        <div class="col-md-12 mt-4">
            <label class="form-label fw-bold">Mood Level</label>
            <input type="range" class="form-range" id="moodRange" name="mood_level" min="1" max="5" step="1" value="<?= intval($entry['mood_level']) ?>">
            <div id="moodLabel" class="fw-bold text-primary"></div>
            <div id="moodDescription" class="text-muted small"></div>
        </div>

        <!-- Images preview -->
        <?php
        $existing_images = json_decode($entry['images'] ?? '[]', true);
        ?>
        <div class="mb-3 mt-4">
            <label class="form-label fw-bold">Images</label>
            <div id="image-upload-container" class="d-flex flex-wrap gap-3">
                <?php foreach ($existing_images as $img): ?>
                    <div class="image-preview">
                        <img src="<?= htmlspecialchars($img) ?>">
                        <input type="hidden" name="existing_images[]" value="<?= htmlspecialchars($img) ?>">
                        <button type="button" class="remove-btn" onclick="this.parentElement.remove()">&times;</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <input type="file" id="imageInput" name="images[]" accept="image/*" hidden multiple>
            <button type="button" class="btn btn-outline-primary mt-2" onclick="document.getElementById('imageInput').click()">
                <i class="bi bi-plus-circle"></i> Add Images
            </button>
        </div>
    </div>

    <!-- Mode -->
    <div class="col-md-6 mt-4">
        <label class="form-label fw-bold">Mode</label>
        <div id="modeToggle" class="btn-group w-100" role="group">
            <input type="radio" class="btn-check" name="mode" id="modeProgram" value="program" <?= $entry['mode'] === 'program' ? 'checked' : '' ?>>
            <label class="btn btn-outline-primary" for="modeProgram">Program</label>

            <input type="radio" class="btn-check" name="mode" id="modeCustom" value="custom" <?= $entry['mode'] === 'custom' ? 'checked' : '' ?>>
            <label class="btn btn-outline-primary" for="modeCustom">Custom</label>
        </div>
    </div>

    <!-- Program -->
    <div id="program-section" style="<?= $entry['mode'] === 'program' ? '' : 'display:none;' ?>">
        <label class="form-label fw-bold mt-4">Choose Program</label>
        <select name="program_id" class="form-select mb-2">
            <option value="">Select</option>
            <?php foreach ($programs_array as $p): ?>
                <option value="<?= $p['id'] ?>" <?= $p['id'] == $entry['program_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['title']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Program Day -->
    <div id="program-day-container" class="mt-2">
        <?php if ($entry['mode'] === 'program'): ?>
            <input type="text" name="program_day" class="form-control" value="<?= htmlspecialchars($entry['program_day']) ?>">
        <?php else: ?>
            <input type="text" name="program_day" class="form-control" placeholder="Custom Day (e.g. Push Day)" value="<?= htmlspecialchars($entry['program_day']) ?>">
        <?php endif; ?>
    </div>

    <!-- Exercise list -->
    <div id="exercise-list-wrapper" class="mt-3">
        <h5>Exercises</h5>
        <div id="exercise-list">
            <?php foreach ($entry_exercises as $i => $ex): ?>
            <?php 
            if (!is_array($ex['sets_json'])) {
                $ex['sets_json'] = [];
            }
            ?>
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <select name="exercises[<?= $i ?>][id]" class="form-select" required>
                            <option value="">Select Exercise</option>
                            <?php foreach ($exercises_array as $e): ?>
                                <option value="<?= $e['id'] ?>" <?= $e['id'] == $ex['exercise_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($e['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <div class="set-list mb-2">
                            <?php foreach ($ex['sets_json'] as $set): ?>
                                <div class="row mb-1">
                                    <div class="col-md-5">
                                        <input type="number" placeholder="Weight" class="form-control set-weight" value="<?= htmlspecialchars($set['weight']) ?>">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="number" placeholder="Reps" class="form-control set-reps" value="<?= htmlspecialchars($set['reps']) ?>">
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.row').remove()">×</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addSet(this)">+ Add Set</button>
                    </div>
                    <input type="hidden" name="exercises[<?= $i ?>][sets_json]" class="sets-json">
                    <div class="col-12"><hr></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Custom Add button -->
    <div id="custom-section" style="<?= $entry['mode'] === 'custom' ? '' : 'display:none;' ?>">
        <button type="button" class="btn btn-outline-primary mt-2" onclick="addExercise()">+ Add Exercise</button>
    </div>

    <!-- Notes -->
    <div class="card border rounded-3 shadow-sm p-3 mt-4">
        <div class="d-flex align-items-center mb-2">
            <i class="bi bi-journal-text fs-5 text-primary me-2"></i>
            <label class="form-label fw-bold mb-0">Notes</label>
        </div>
        <textarea name="notes" class="form-control" rows="4"><?= htmlspecialchars($entry['notes']) ?></textarea>
    </div>

    <!-- Submit -->
    <div class="text-end mt-4">
        <button type="submit" class="btn btn-success btn-md rounded-pill px-4 shadow-sm">
            <i class="bi bi-check-circle me-2"></i> Update Entry
        </button>
    </div>
</form>

</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>


<script>
const moodDescriptions = {
  1: { label: "Very Low", description: "Felt tired, unmotivated, or stressed during training." },
  2: { label: "Low", description: "Low energy or mild frustration." },
  3: { label: "Neutral", description: "Average energy and motivation level." },
  4: { label: "Good", description: "Felt focused and productive." },
  5: { label: "Excellent", description: "High energy and performance!" }
};

function updateMoodDisplay(val) {
  const info = moodDescriptions[val];
  if (info) {
    document.getElementById("moodLabel").textContent = info.label;
    document.getElementById("moodDescription").textContent = info.description;
  }
}
const moodRange = document.getElementById("moodRange");
updateMoodDisplay(moodRange.value);
moodRange.addEventListener("input", () => updateMoodDisplay(moodRange.value));

// Добавление подхода
function addSet(button) {
  const setList = button.parentElement.querySelector('.set-list');
  const div = document.createElement('div');
  div.classList.add('row', 'mb-1');
  div.innerHTML = `
    <div class="col-md-5">
        <input type="number" placeholder="Weight" class="form-control set-weight">
    </div>
    <div class="col-md-5">
        <input type="number" placeholder="Reps" class="form-control set-reps">
    </div>
    <div class="col-md-2 text-end">
        <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.row').remove()">×</button>
    </div>`;
  setList.appendChild(div);
}

// Добавление нового упражнения (в custom режиме)
let exerciseIndex = <?= count($entry_exercises) ?>;
function addExercise() {
  const container = document.getElementById("exercise-list");
  const div = document.createElement("div");
  div.classList.add("row", "g-2", "mb-2");
  div.innerHTML = `
    <div class="col-md-4">
        <select name="exercises[${exerciseIndex}][id]" class="form-select" required>
            <option value="">Select Exercise</option>
            <?php
            $exercises->data_seek(0);
            while ($e = $exercises->fetch_assoc()):
            ?>
              <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['name']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-md-8">
        <div class="set-list mb-2"></div>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addSet(this)">+ Add Set</button>
    </div>
    <input type="hidden" name="exercises[${exerciseIndex}][sets_json]" class="sets-json">
    <div class="col-12"><hr></div>`;
  container.appendChild(div);
  exerciseIndex++;
}

// Перед отправкой формы — собираем JSON из подходов
document.querySelector("form").addEventListener("submit", function () {
  document.querySelectorAll(".sets-json").forEach(hiddenInput => {
    const rows = hiddenInput.closest(".row").querySelectorAll(".set-list .row");
    const sets = [];
    rows.forEach(r => {
      const weight = r.querySelector(".set-weight").value;
      const reps = r.querySelector(".set-reps").value;
      if (weight || reps) sets.push({ weight, reps });
    });
    hiddenInput.value = JSON.stringify(sets);
  });
});
</script>

</body>
</html>
