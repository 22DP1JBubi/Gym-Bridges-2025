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

if (!isset($_GET['ajax'])) {
    include 'includes/header.php';
}



if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);

    // Убедимся, что запись принадлежит пользователю
    $check = $conn->query("SELECT id FROM training_diary WHERE id = $delete_id AND user_id = $user_id");
    if ($check->num_rows > 0) {
        $conn->query("DELETE FROM training_diary_exercises WHERE diary_id = $delete_id");
        $conn->query("DELETE FROM training_diary WHERE id = $delete_id");
    }

    header("Location: training_diary.php");
    exit();
}


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_diary'])) {
    $date = $_POST['training_date'] ?? date('Y-m-d');
    $mode = $_POST['mode'] ?? 'custom';
    $program_id = ($mode === 'program' && !empty($_POST['program_id'])) ? intval($_POST['program_id']) : null;
    $program_day = !empty($_POST['program_day']) ? $_POST['program_day'] : null;
    $notes = $_POST['notes'] ?? '';
    $mood = !empty($_POST['mood_level']) ? intval($_POST['mood_level']) : null;
    $images = [];

    $hours = isset($_POST['duration_hours']) ? intval($_POST['duration_hours']) : 0;
    $minutes = isset($_POST['duration_minutes']) ? intval($_POST['duration_minutes']) : 0;

    if ($minutes > 59) $minutes = 59;
    if ($hours > 12) $hours = 12;

    $training_time = sprintf('%02d:%02d:00', $hours, $minutes);  // формат TIME


    // Upload images
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
    }

    $img_json = json_encode($images);

    // Prepare and bind with correct types
    $stmt = $conn->prepare("INSERT INTO training_diary 
        (user_id, training_date, mode, program_id, program_day, mood_level, notes, images, training_time) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ississsss", 
        $user_id,        // i - user_id
        $date,           // s - training_date
        $mode,           // s - mode
        $program_id,     // s - program_id (может быть NULL)
        $program_day,    // s - program_day (может быть NULL)
        $mood,           // i - mood_level
        $notes,          // s - notes
        $img_json,        // s - images
        $training_time
    );
    $stmt->execute();
    $diary_id = $stmt->insert_id;
    $stmt->close();

    // Save exercises
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
            $stmt->bind_param("iissss", $diary_id, $ex_id, $weight, $sets, $reps, $sets_json);
            $stmt->execute();
            $stmt->close();
        }
    }

    header("Location: training_diary.php");
    exit();
}


$programs = $conn->query("SELECT id, title FROM workout_programs ORDER BY title ASC");
$exercises = $conn->query("SELECT id, name FROM exercises ORDER BY name ASC");
$diary_entries = $conn->query("
    SELECT td.*, wp.title AS program_title 
    FROM training_diary td 
    LEFT JOIN workout_programs wp ON td.program_id = wp.id 
    WHERE td.user_id = $user_id 
    ORDER BY td.training_date DESC
");



// Обработка AJAX-запроса на получение дней программы
if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_program_days' && isset($_GET['id'])) {
    $program_id = intval($_GET['id']);
    $res = $conn->query("SELECT days_json FROM workout_programs WHERE id = $program_id LIMIT 1");

    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $json = json_decode($row['days_json'], true);
        $days = array_map(fn($d) => $d['title'], $json);
        echo json_encode($days);
    } else {
        echo json_encode([]);
    }
    exit;
}

// Возвращает полный days_json (для упражнений)
if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_program_days_full' && isset($_GET['id'])) {
    $program_id = intval($_GET['id']);
    $res = $conn->query("SELECT days_json FROM workout_programs WHERE id = $program_id LIMIT 1");

    if ($res && $res->num_rows > 0) {
        echo $res->fetch_assoc()['days_json'];
    } else {
        echo json_encode([]);
    }
    exit;
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
    <link rel="stylesheet" type="text/css" href="style.css">

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
        <h1 class="mb-4 text-center">
            <i class="bi bi-journal-text me-2 text-primary"></i>
            Training Diary Entry
        </h1>

        <div class="alert alert-info" role="alert">
            This training diary lets you log your daily workouts, track your mood, upload images, and save exercises from training programs or create custom workouts.  
            You can switch between <strong>program</strong> and <strong>custom</strong> mode, attach notes, and monitor your training journey.
        </div>


        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="save_diary" value="1">

            <div class="row mb-3">
                <div class="col-md-6 mt-4">
                    <label class="form-label fw-bold">Date</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light" id="calendar-addon" style="cursor: pointer;">
                            <i class="bi bi-calendar-event"></i>
                        </span>
                        <input type="text" id="training_date" name="training_date" class="form-control" placeholder="Select a date" required aria-describedby="calendar-addon">
                    </div>

                </div>


                <div class="col-md-6 mt-4">
                     <label class="form-label fw-bold">
                        <i class="bi bi-stopwatch me-1"></i> Duration of Workout
                    </label>
                    <div class="d-flex flex-wrap gap-3">
                        <div class="input-group" style="max-width: 160px;">
                        <span class="input-group-text bg-light"><i class="bi bi-hourglass-top"></i></span>
                        <input type="number" name="duration_hours" class="form-control" min="0" max="12" value="0" required>
                        <span class="input-group-text">hrs</span>
                        </div>

                        <div class="input-group" style="max-width: 160px;">
                        <span class="input-group-text bg-light"><i class="bi bi-clock"></i></span>
                        <input type="number" name="duration_minutes" class="form-control" min="0" max="59" value="0" required>
                        <span class="input-group-text">min</span>
                        </div>
                    </div>
                    <div class="form-text text-muted mt-1">You can enter any duration, e.g. 1 hr 5 min, 0 hr 42 min, etc.</div>
                </div>

                <div class="col-md-12 mt-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mood Level</label>
                        <input type="range" class="form-range" id="moodRange" name="mood_level" min="1" max="5" step="1" value="3">
                        <div id="moodLabel" class="fw-bold text-primary">Neutral</div>
                        <div id="moodDescription" class="text-muted small">Average energy and motivation level.</div>
                    </div>

                </div>
                <div class="mb-3 mt-4" >
                    <label class="form-label fw-bold">Images</label>
                    <div id="image-upload-container" class="d-flex flex-wrap gap-3">
                        <!-- Здесь будут превью -->
                    </div>
                    <input type="file" id="imageInput" name="images[]" accept="image/*" hidden multiple>
                    <button type="button" class="btn btn-outline-primary mt-2" onclick="document.getElementById('imageInput').click()">
                        <i class="bi bi-plus-circle"></i> Add Images
                    </button>
                    <div class="form-text text-muted">
                        Max 20 images, total size must not exceed 40MB.
                    </div>

                </div>

            </div>

            <div class="col-md-6 mt-4">
                <label class="form-label fw-bold">
                    Mode
                    <i class="bi bi-info-circle text-primary ms-1" data-bs-toggle="modal" data-bs-target="#modeInfoModal" style="cursor: pointer;"></i>
                </label>
                <div id="modeToggle" class="btn-group w-100" role="group">
                    <input type="radio" class="btn-check" name="mode" id="modeProgram" value="program" autocomplete="off" checked>
                    <label class="btn btn-outline-primary" for="modeProgram">Program</label>

                    <input type="radio" class="btn-check" name="mode" id="modeCustom" value="custom" autocomplete="off">
                    <label class="btn btn-outline-primary" for="modeCustom">Custom</label>
                </div>
            </div>


            <div id="program-section">
                <label class="form-label fw-bold mt-4">Choose Program</label>
                <select name="program_id" class="form-select mb-2">
                    <option value="">Select</option>
                    <?php while ($p = $programs->fetch_assoc()): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['title']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div id="program-day-container" class="mt-2">
                <input type="text" name="program_day" class="form-control" placeholder="Program Day (e.g. Day 1 – Upper Power)">
            </div>


            <!-- Отдельный контейнер для упражнений (виден всегда) -->
            <div id="exercise-list-wrapper" class="mt-3">
                <h5>Exercises</h5>
                <div id="exercise-list"></div>
            </div>

            <!-- Кнопка добавления только для кастомного режима -->
            <div id="custom-section" style="display:none;">
                <button type="button" class="btn btn-outline-primary mt-2" onclick="addExercise()">+ Add Exercise</button>
            </div>


            <div class="card border rounded-3 shadow-sm p-3 mt-4">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-journal-text fs-5 text-primary me-2"></i>
                    <label class="form-label fw-bold mb-0">Notes</label>
                </div>
                <textarea name="notes" class="form-control" rows="4" placeholder="Write your thoughts, observations or anything you want to track..."></textarea>
            </div>


            <div class="text-end mt-4">
                <button type="submit" class="btn btn-success btn-md rounded-pill px-4 shadow-sm">
                    <i class="bi bi-check-circle me-2"></i> Save Entry
                </button>
            </div>


        </form>
    </div>

    
    <div class="card shadow-sm p-4 my-4">
        <div class="text-center mb-3">
            <div class="d-flex justify-content-center align-items-center mb-2">
            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 50px; height: 50px;">
                <i class="bi bi-journal-text" style="font-size: 1.4rem;"></i>
            </div>
            <h4 class="mb-0">Saved Diary Entries</h4>
            </div>
            <p class="text-muted mb-0">Here you can find your recent training notes and reflections.</p>
        </div>
        
        <div class="d-flex gap-2 mb-3">
            <button id="sortByTrainingDate" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-calendar"></i> Sort by Training Date <span id="iconTrainingDate" class="bi"></span>
            </button>

            <button id="sortByCreatedAt" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-clock-history"></i> Sort by Created <span id="iconCreatedAt" class="bi"></span>
            </button>

            <button id="sortByMood" class="btn btn-sm btn-outline-success">
                <i class="bi bi-emoji-smile"></i> Sort by Mood <span id="iconMood" class="bi"></span>
            </button>

            <button id="sortByTime" class="btn btn-sm btn-outline-dark">
                <i class="bi bi-stopwatch"></i> Sort by Time <span id="iconTime" class="bi"></span>
            </button>
        </div>

<div id="pdf-diary-content">
        <div class="card mt-4">
            <div class="card-header bg-light fw-bold d-none d-lg-flex">
                <div class="col-lg-2">Date</div>
                <div class="col-lg-1">Mode</div>
                <div class="col-lg-2">Program</div>
                <div class="col-lg-2">Day</div>
                <div class="col-lg-1">Mood</div>
                <div class="col-lg-1">Time</div>
                <div class="col-lg-1">Created At</div>
                <div class="col-lg-2 text-end">Actions</div>
            </div>
            <div class="list-group list-group-flush">
                <?php while ($entry = $diary_entries->fetch_assoc()): ?>
                    <div class="list-group-item d-flex flex-wrap align-items-center diary-entry-row"
                        data-training-date="<?= htmlspecialchars($entry['training_date']) ?>"
                        data-created-at="<?= htmlspecialchars($entry['created_at']) ?>"
                        data-mood="<?= htmlspecialchars($entry['mood_level']) ?>"
                        data-time="<?= htmlspecialchars($entry['training_time']) ?>">

                        <div class="col-lg-2 fw-bold col-4">
                            <i class="bi bi-calendar-event text-primary me-1"></i>
                            <?= htmlspecialchars($entry['training_date']) ?>
                        </div>
                        <div class="col-lg-1 col-4">
                            <span class="badge bg-<?= $entry['mode'] === 'program' ? 'primary' : 'secondary' ?>">
                                <?= ucfirst($entry['mode']) ?>
                            </span>
                        </div>
                        <div class="col-lg-2 text-muted col-4">
                            <?= $entry['program_title'] ? htmlspecialchars($entry['program_title']) : '-' ?>
                        </div>
                        <div class="col-lg-2 col-4">
                            <?= $entry['program_day'] ? htmlspecialchars($entry['program_day']) : '-' ?>
                        </div>
                        <div class="col-lg-1 col-4">
                            <?= $entry['mood_level'] ?? '-' ?>/5
                        </div>
                        <div class="col-lg-1 col-4">
                            <?= $entry['training_time'] ? htmlspecialchars($entry['training_time']) : '-' ?>
                        </div>
                        <div class="col-lg-1 small text-muted col-6">
                            <?= date('Y-m-d H:i', strtotime($entry['created_at'] ?? $entry['training_date'])) ?>
                        </div>
                        <div class="col-lg-2 text-end col-6">
                            <a href="training_diary_view.php?id=<?= $entry['id'] ?>" class="btn btn-sm btn-outline-info" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="training_diary_edit.php?id=<?= $entry['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="training_diary.php?delete=<?= $entry['id'] ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this entry?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
        <div class="text-end mb-3">
            <button class="btn btn-danger" onclick="generatePDF()">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Download Diary as PDF
            </button>
        </div>


    </div>
</div>
</div>


<div class="modal fade" id="modeInfoModal" tabindex="-1" aria-labelledby="modeInfoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modeInfoModalLabel">What is Mode: Program vs Custom?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        <p>This section lets you choose how to build your training diary entry:</p>

        <hr>

        <h6><i class="bi bi-list-check text-primary me-1"></i> Program Mode</h6>
        <ul>
          <li>Select a predefined workout program.</li>
          <li>Choose from structured days (e.g., "Day 1 – Upper Power").</li>
          <li>Exercises are automatically loaded based on the selected day.</li>
          <li>Ideal for following a systematic training plan.</li>
        </ul>

        <hr>

        <h6><i class="bi bi-pencil text-success me-1"></i> Custom Mode</h6>
        <ul>
          <li>Build your own workout from scratch.</li>
          <li>Select any exercises manually from the database.</li>
          <li>Add sets, reps, weights, notes, and attach images freely.</li>
          <li>Perfect for freestyle workouts, testing, or recording one-off training sessions.</li>
        </ul>

        <hr>
        <p class="text-muted small">
          You can switch between modes anytime. Program mode helps you stay consistent, while Custom mode offers flexibility.
        </p>

      </div>
    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>




<script>
const exercises = <?php
$exercises->data_seek(0);
echo json_encode($exercises->fetch_all(MYSQLI_ASSOC));
?>;

let exerciseIndex = 0; // глобально

function addExercise() {
    const container = document.getElementById("exercise-list");

    const index = exerciseIndex++; // каждый раз увеличиваем

    const div = document.createElement("div");
    div.classList.add("row", "g-2", "mb-2");
    div.innerHTML = `
        <div class="col-md-4">
            <select name="exercises[${index}][id]" class="form-select" required>
                <option value="">Select Exercise</option>
                ${exercises.map(e => `<option value="${e.id}">${e.name}</option>`).join('')}
            </select>
        </div>
        <div class="col-md-8">
            <div class="set-list mb-2"></div>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addSet(this)">+ Add Set</button>
        </div>
        <input type="hidden" name="exercises[${index}][sets_json]" class="sets-json">
        <div class="col-12"><hr></div>
    `;

    container.appendChild(div);
}


// Toggle mode
const modeRadios = document.querySelectorAll('input[name="mode"]');
modeRadios.forEach(r => r.addEventListener('change', () => {
    const isProgram = r.value === 'program';
    document.getElementById('program-section').style.display = isProgram ? 'block' : 'none';
    document.getElementById('custom-section').style.display = !isProgram ? 'block' : 'none';

    // Переключение поля выбора дня
   const dayContainer = document.getElementById("program-day-container");

    if (isProgram) {
        dayContainer.innerHTML = `
            <select name="program_day" class="form-select mt-2">
                <option value="">Select Day</option>
            </select>`;
    } else {
        dayContainer.innerHTML = `
            <input type="text" name="program_day" class="form-control mt-2" placeholder="Custom Day (e.g. Push Day)">`;

        // Также очищаем список упражнений
        document.getElementById("exercise-list").innerHTML = '';
    }
}));

</script>


<script>
// Когда пользователь выбирает программу
document.querySelector('select[name="program_id"]').addEventListener("change", function () {
    const programId = this.value;
    const container = document.getElementById("program-day-container");

    fetch(`training_diary.php?ajax=get_program_days&id=${programId}`)
        .then(res => res.json())
        .then(days => {
            if (days.length > 0) {
                // Строим <select> и скрытый input для сохранения значения
                const select = document.createElement("select");
                select.name = "program_day";
                select.className = "form-select mt-2";
                select.innerHTML = '<option value="">Select Day</option>' + days.map(day =>
                    `<option value="${day}">${day}</option>`
                ).join('');

                select.addEventListener("change", function () {
                    loadProgramDayExercises(this.value);
                });

               

                container.innerHTML = '';
                container.appendChild(select);
            } else {
                container.innerHTML = '<input type="text" name="program_day" class="form-control mt-2" placeholder="Program Day">';
            }

            // Сброс упражнений
            document.getElementById("exercise-list").innerHTML = '';
        });
});

</script>

<script>
// Загружает упражнения из выбранного дня программы
function loadProgramDayExercises(dayTitle) {
    const programId = document.querySelector('select[name="program_id"]').value;
    const container = document.getElementById("exercise-list");
    container.innerHTML = '';
    exerciseIndex = 0; // сбрасываем индекс

    fetch(`training_diary.php?ajax=get_program_days_full&id=${programId}`)
        .then(res => res.json())
        .then(json => {
            const day = json.find(d => d.title === dayTitle);
            if (!day) return;
            day.exercises.forEach(ex => {
                const index = exerciseIndex++; // для каждой записи уникальный индекс

                const div = document.createElement("div");
                div.classList.add("row", "g-2", "mb-2");
                div.innerHTML = `
                    <div class="col-md-4">
                        <select name="exercises[${index}][id]" class="form-select" required>
                            ${exercises.map(e => `
                                <option value="${e.id}" ${e.id == ex.exercise_id ? 'selected' : ''}>${e.name}</option>
                            `).join('')}
                        </select>
                    </div>
                    <div class="col-md-8">
                        <div class="set-list mb-2">
                            <div class="row mb-1">
                                <div class="col-md-5">
                                    <input type="number" placeholder="Weight" class="form-control set-weight">
                                </div>
                                <div class="col-md-5">
                                    <input type="text" placeholder="Reps" class="form-control set-reps" value="${ex.reps}">
                                </div>
                                <div class="col-md-2 text-end">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.row').remove()">×</button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addSet(this)">+ Add Set</button>
                    </div>
                    <input type="hidden" name="exercises[${index}][sets_json]" class="sets-json">
                    <div class="col-12"><hr></div>
                `;
                container.appendChild(div);
            });
        });
}


// Функция для получения названия упражнения по ID
function getExerciseName(id) {
    const ex = exercises.find(e => e.id == id);
    return ex ? ex.name : 'Exercise';
}
</script>

<script>
function addSet(button) {
    const setList = button.parentElement.querySelector('.set-list');
    const index = setList.children.length + 1;
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
        </div>
    `;
    setList.appendChild(div);
}
</script>

<script>
document.querySelector("form").addEventListener("submit", function () {
    imageInput.files = imageFiles.files;

    document.querySelectorAll(".sets-json").forEach(hiddenInput => {
        const setList = hiddenInput.closest(".row").querySelectorAll(".set-list .row");
        const sets = [];
        setList.forEach(row => {
            const weight = row.querySelector(".set-weight").value;
            const reps = row.querySelector(".set-reps").value;
            if (weight || reps) {
                sets.push({ weight, reps });
            }
        });
        hiddenInput.value = JSON.stringify(sets);
    });
});
</script>

<script>
  const flatpickrInstance = flatpickr("#training_date", {
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "F j, Y",
    maxDate: "today",
    defaultDate: "today"
  });

  document.getElementById("calendar-addon").addEventListener("click", function () {
    flatpickrInstance.open();
  });
</script>

<script>
const moodDescriptions = {
  1: {
    label: "Very Low",
    description: "Felt tired, unmotivated, or stressed during training."
  },
  2: {
    label: "Low",
    description: "Low energy or mild frustration. Some effort needed to get through."
  },
  3: {
    label: "Neutral",
    description: "Average energy and motivation level."
  },
  4: {
    label: "Good",
    description: "Felt focused and productive during the session."
  },
  5: {
    label: "Excellent",
    description: "High energy, motivation and performance!"
  }
};

const moodRange = document.getElementById("moodRange");
const moodLabel = document.getElementById("moodLabel");
const moodDescription = document.getElementById("moodDescription");

function updateMoodDisplay(val) {
  const info = moodDescriptions[val];
  moodLabel.textContent = info.label;
  moodDescription.textContent = info.description;
}

// Обновляем при изменении
moodRange.addEventListener("input", () => {
  updateMoodDisplay(moodRange.value);
});

// Обновляем при загрузке
updateMoodDisplay(moodRange.value);
</script>

<script>

const imageInput = document.getElementById("imageInput");
const imageContainer = document.getElementById("image-upload-container");
let imageFiles = new DataTransfer();

imageInput.addEventListener("change", function () {
  for (const file of this.files) {
    // Проверим, не добавлен ли уже этот файл
    if ([...imageFiles.files].some(f => f.name === file.name)) continue;

    imageFiles.items.add(file);

    const reader = new FileReader();
    reader.onload = function (e) {
      const wrapper = document.createElement("div");
      wrapper.className = "image-preview";

      const img = document.createElement("img");
      img.src = e.target.result;

      const btn = document.createElement("button");
      btn.innerHTML = "&times;";
      btn.className = "remove-btn";
      btn.onclick = () => {
        imageContainer.removeChild(wrapper);
        imageFiles.items.remove([...imageFiles.files].findIndex(f => f.name === file.name));
        imageInput.files = imageFiles.files;
      };

      wrapper.appendChild(img);
      wrapper.appendChild(btn);
      imageContainer.appendChild(wrapper);
    };
    reader.readAsDataURL(file);
  }

  // Обновим input
  imageInput.files = imageFiles.files;
});

</script>

<script>
flatpickr("#training_time", {
  enableTime: true,
  noCalendar: true,
  dateFormat: "H:i",
  time_24hr: true,
  defaultDate: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
});

</script>

<script>
async function generatePDF() {
    if (!window.jspdf || !window.jspdf.jsPDF) {
        alert("jsPDF not loaded");
        return;
    }

    const { jsPDF } = window.jspdf;

    const diaryElement = document.getElementById("pdf-diary-content");
    const nickname = "<?= $_SESSION['username'] ?? 'User' ?>"; // или подставь реальное имя
    const date = new Date().toLocaleDateString();
    if (!diaryElement) {
        alert("Diary not found");
        return;
    }

    // Загружаем логотип
    const logoUrl = 'images/logo_black_2.png'; // путь к логотипу
    const img = new Image();
    img.src = logoUrl;
    await img.decode();

    html2canvas(diaryElement).then(canvas => {
        const pdf = new jsPDF('p', 'mm', 'a4');
        const imgData = canvas.toDataURL('image/png');

        const pageWidth = pdf.internal.pageSize.getWidth();
        const imgWidth = pageWidth - 20;
        const imgHeight = imgWidth * (canvas.height / canvas.width);

        const logoWidth = 35; // желаемая ширина
        const aspectRatio = img.height / img.width;
        const logoHeight = logoWidth * aspectRatio;

        pdf.addImage(img, 'PNG', 10, 10, logoWidth, logoHeight);


        // заголовок
        // pdf.setFontSize(16);
        // pdf.text('Training Diary Report', pageWidth / 2, 25, { align: 'center' });

        // Заголовок
        pdf.setFontSize(16);
        pdf.setFont("helvetica", "bold");
        pdf.text(`Training Diary Report for ${nickname}`, 105, 30, { align: "center" });

        // Дата создания
        pdf.setFontSize(10);
        pdf.setFont("helvetica", "normal");
        pdf.text(`Generated on: ${date}`, 105, 36, { align: "center" });

        // сам дневник
        pdf.addImage(imgData, 'PNG', 10, 40, imgWidth, imgHeight);

        pdf.save('training_diary.pdf');
    });
}

</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>
