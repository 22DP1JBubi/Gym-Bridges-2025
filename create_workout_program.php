<?php
$conn = new mysqli("localhost", "root", "", "gymbridges");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
mysqli_set_charset($conn, "utf8mb4");


// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $id = intval($_POST['edit_id']);
    $title = $_POST['title'] ?? '';
    $tips = $_POST['tips'] ?? '';
    $goal = $_POST['goal'] ?? '';
    $type = $_POST['type'] ?? '';
    $level = $_POST['level'] ?? 'Beginner';
    $duration = $_POST['duration'] ?? '';
    $days_per_week = (int) ($_POST['days_per_week'] ?? 0);
    $target_group = $_POST['target_group'] ?? '';
    $description = $_POST['description'] ?? '';
    $muscle_category = $_POST['muscle_category'] ?? '';
    $muscle_group = $_POST['muscle_group'] ?? '';
    $days_exercises_json = $_POST['days_exercises_json'] ?? '{}';

    // Upload image if new one provided
    $image = $_POST['existing_image'] ?? '';
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        $image = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image);
    }

    $stmt = $conn->prepare("UPDATE workout_programs SET title=?, description=?, tips=?, image=?, goal=?, type=?, level=?, days_per_week=?, duration_weeks=?, target_group=?, muscle_categories=?, muscle_groups=?, days_json=? WHERE id=?");
    $stmt->bind_param("sssssssisssssi", 
        $title, 
        $description, 
        $tips,
        $image, 
        $goal, 
        $type, 
        $level, 
        $days_per_week, 
        $duration, 
        $target_group, 
        $muscle_category, 
        $muscle_group, 
        $days_exercises_json,
        $id
    );
    $stmt->execute();
    $stmt->close();

    header("Location: create_workout_program.php");
    exit();
}

// Fetch program to edit
$editProgram = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM workout_programs WHERE id = $id LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $editProgram = $result->fetch_assoc();
    }
}





if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_submit'])) {
    $title = $_POST['title'] ?? '';
    $tips = $_POST['tips'] ?? '';
    $goal = $_POST['goal'] ?? '';
    $type = $_POST['type'] ?? '';
    $level = $_POST['level'] ?? 'Beginner';
    $duration = $_POST['duration'] ?? '';
    $days_per_week = (int) ($_POST['days_per_week'] ?? 0);
    $target_group = $_POST['target_group'] ?? '';
    $description = $_POST['description'] ?? '';
    $muscle_category = $_POST['muscle_category'] ?? '';
    $muscle_group = $_POST['muscle_group'] ?? '';
    $days_exercises_json = $_POST['days_exercises_json'] ?? '{}';

    // Upload image
    $image = '';
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        $image = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image);
    }

    $stmt = $conn->prepare("INSERT INTO workout_programs 
        (title, description, tips, image, goal, type, level, days_per_week, duration_weeks, target_group, muscle_categories, muscle_groups, days_json) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("sssssssisssss", 
        $title, 
        $description, 
        $tips,
        $image, 
        $goal, 
        $type, 
        $level, 
        $days_per_week, 
        $duration, 
        $target_group, 
        $muscle_category, 
        $muscle_group, 
        $days_exercises_json
    );

    $stmt->execute();
    $stmt->close();

    header("Location: create_workout_program.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM workout_programs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: create_workout_program.php");
    exit();
}



$exercises = $conn->query("SELECT id, name FROM exercises ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Workout Program</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    .form-section { display: none; }
    .form-section.active { display: block; }
  </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    <button class="btn btn-primary mb-3" onclick="toggleForm('createForm')">
        <i class="bi bi-plus-circle"></i> Create Workout Program
    </button>

    <div id="createForm" class="form-section">
    <h2>Create Workout Program</h2>
    <form method="POST" enctype="multipart/form-data" onsubmit="return saveDaysExercises();">
        <div class="card p-4">
        <div class="row g-3">
            <div class="row g-3">

                <div class="col-md-6">
                    <label for="title" class="form-label">Program Name</label>
                    <input id="title" name="title" class="form-control" placeholder="Program name" required>

                </div>

                <div class="col-md-6">
                    <label for="goal" class="form-label">Goal</label>
                    <select name="goal" id="goal" class="form-select" required>
                    <option value="" disabled selected>Choose goal</option>
                    <option value="Fat Loss">Fat Loss</option>
                    <option value="Muscle Gain">Muscle Gain</option>
                    <option value="Strength Building">Strength Building</option>
                    <option value="Endurance Improvement">Endurance Improvement</option>
                    <option value="Toning">Toning</option>
                    <option value="Mobility & Flexibility">Mobility & Flexibility</option>
                    <option value="Strength & Muscle Gain">Strength & Muscle Gain</option>
                    <option value="Rehabilitation">Rehabilitation</option>
                    <option value="Weight Maintenance">Weight Maintenance</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="type" class="form-label">Type</label>
                    <select name="type" id="type" class="form-select" required>
                    <option value="" disabled selected>Choose type</option>
                    <option value="Cardio">Cardio</option>
                    <option value="Strength Training">Strength Training</option>
                    <option value="Powerbuilding">Powerbuilding</option>
                    <option value="Bodybuilding-oriented">Bodybuilding-oriented</option>
                    <option value="HIIT">HIIT</option>
                    <option value="Bodyweight">Bodyweight</option>
                    <option value="Gym-based">Gym-based</option>
                    <option value="Home-based">Home-based</option>
                    <option value="Stretching">Stretching</option>
                    <option value="CrossFit">CrossFit</option>
                    <option value="Pilates">Pilates</option>
                    <option value="Yoga">Yoga</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="level" class="form-label">Level</label>
                    <select name="level" id="level" class="form-select" required>
                    <option value="" disabled selected>Select level</option>
                    <option value="Beginner">Beginner</option>
                    <option value="Intermediate">Intermediate</option>
                    <option value="Expert">Expert</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="duration" class="form-label">Duration (weeks)</label>
                    <input type="text" name="duration" id="duration" class="form-control" placeholder="e.g. 6 or 6-12" required>

                </div>

                <div class="col-md-6">
                    <label for="days_per_week" class="form-label">Days per Week</label>
                    <input type="number" name="days_per_week" id="days_per_week" class="form-control" min="1" max="7" required>
                </div>


                <div class="col-md-6">
                    <label for="target_group" class="form-label">Target Group</label>
                    <select name="target_group" id="target_group" class="form-select" required>
                    <option value="" disabled selected>Select target group</option>
                    <option value="Men">Men</option>
                    <option value="Women">Women</option>
                    <option value="All">All</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="image" class="form-label">Program Image</label>
                    <input type="file" name="image" id="image" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Muscle Category</label>
                    <div id="category_buttons" class="d-flex flex-wrap gap-2"></div>
                    <div id="category_order_display" class="mt-2 text-muted small"></div>
                    <input type="hidden" name="muscle_category" id="category_hidden">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Muscle Groups</label>
                    <div id="muscle_group_buttons" class="d-flex flex-wrap gap-2"></div>
                    <div id="muscle_order_display" class="mt-2 text-muted small"></div>
                    <input type="hidden" name="muscle_group" id="muscle_group_hidden">
                </div>

                <div class="col-12">
                    <label for="description" class="form-label">Program Description</label>
                    <textarea name="description" id="description" class="form-control" placeholder="Describe the program..." rows="3"></textarea>
                </div>

                <div class="col-12">
                  <label for="tips" class="form-label">Coach's Tips</label>
                  <textarea name="tips" id="tips" class="form-control" rows="3" placeholder="E.g., Start light. Focus on form. Rest between sets."></textarea>
                </div>



            </div>


        <hr>
            <div class="mt-5">
            <h5>Plan Days and Exercises</h5>
            <div id="days-container"></div>
            <button type="button" class="btn btn-secondary my-3" onclick="addDay()">+ Add Day</button>

            <input type="hidden" name="days_exercises_json" id="days_exercises_json">
            <input type="hidden" name="create_submit" value="1">
            <button type="submit" class="btn btn-success">Save Program</button>
            
            </div>

            <!-- Шаблоны -->
            <template id="day-template">
            <div class="day-item border rounded p-3 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                <input type="text" class="form-control day-title" placeholder="Day title (e.g. Chest Day)">
                <button type="button" class="btn btn-outline-danger btn-sm ms-2 remove-day-btn">Remove Day</button>
                </div>
                <div class="exercise-list"></div>
                <button type="button" class="btn btn-sm btn-outline-primary add-exercise-btn mt-2">+ Add Exercise</button>
            </div>
            </template>

            <template id="exercise-template">
            <div class="exercise-row row g-2 align-items-center mt-2">
                <div class="col-md-4">
                <select class="form-select exercise-muscle-filter">
                    <option value="">Filter by muscle</option>
                </select>
                </div>
                <div class="col-md-4">
                <select class="form-select exercise-select">
                    <option value="">Select exercise</option>
                </select>
                </div>

                    <div class="col-md-2">
                      <input type="text" class="form-control sets" placeholder="Sets (e.g. 4 or 3-5)">
                    </div>

                    <div class="col-md-2">
                      <input type="text" class="form-control reps" placeholder="Reps (e.g. 10 or 8-12)">
                    </div>

                <div class="col-md-12 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-exercise-btn">Remove</button>
                </div>
            </div>
            </template>
    </form>
    </div>
</div>
</div>




<?php if ($editProgram): ?>
<div id="editForm" class="form-section edit-form-container active">
  <div class="card p-4 mb-4">
    <h4>Edit Program #<?= $editProgram['id'] ?></h4>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="edit_id" value="<?= $editProgram['id'] ?>">

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Title</label>
                <input name="title" class="form-control" value="<?= htmlspecialchars($editProgram['title']) ?>">
            </div>

            <div class="col-md-6">
              <label class="form-label">Goal</label>
                <select name="goal" class="form-select" required>
                    <option value="" disabled <?= $editProgram['goal'] == '' ? 'selected' : '' ?>>Choose goal</option>
                    <option value="Fat Loss" <?= $editProgram['goal'] == 'Fat Loss' ? 'selected' : '' ?>>Fat Loss</option>
                    <option value="Muscle Gain" <?= $editProgram['goal'] == 'Muscle Gain' ? 'selected' : '' ?>>Muscle Gain</option>
                    <option value="Strength Building" <?= $editProgram['goal'] == 'Strength Building' ? 'selected' : '' ?>>Strength Building</option>
                    <option value="Endurance Improvement" <?= $editProgram['goal'] == 'Endurance Improvement' ? 'selected' : '' ?>>Endurance Improvement</option>
                    <option value="Toning" <?= $editProgram['goal'] == 'Toning' ? 'selected' : '' ?>>Toning</option>
                    <option value="Mobility & Flexibility" <?= $editProgram['goal'] == 'Mobility & Flexibility' ? 'selected' : '' ?>>Mobility & Flexibility</option>
                    <option value="Strength & Muscle Gain" <?= $editProgram['goal'] == 'Strength & Muscle Gain' ? 'selected' : '' ?>>Strength & Muscle Gain</option>
                    <option value="Rehabilitation" <?= $editProgram['goal'] == 'Rehabilitation' ? 'selected' : '' ?>>Rehabilitation</option>
                    <option value="Weight Maintenance" <?= $editProgram['goal'] == 'Weight Maintenance' ? 'selected' : '' ?>>Weight Maintenance</option>
                </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Type</label>
                <select name="type" class="form-select" required>
                    <option value="" disabled <?= $editProgram['type'] == '' ? 'selected' : '' ?>>Choose type</option>
                    <option value="Cardio" <?= $editProgram['type'] == 'Cardio' ? 'selected' : '' ?>>Cardio</option>
                    <option value="Strength Training" <?= $editProgram['type'] == 'Strength Training' ? 'selected' : '' ?>>Strength Training</option>
                    <option value="HIIT" <?= $editProgram['type'] == 'HIIT' ? 'selected' : '' ?>>HIIT</option>
                    <option value="Bodybuilding-oriented" <?= $editProgram['type'] == 'Bodybuilding-oriented' ? 'selected' : '' ?>>Bodybuilding-oriented</option>
                    <option value="Bodyweight" <?= $editProgram['type'] == 'Bodyweight' ? 'selected' : '' ?>>Bodyweight</option>
                    <option value="Gym-based" <?= $editProgram['type'] == 'Gym-based' ? 'selected' : '' ?>>Gym-based</option>
                    <option value="Powerbuilding" <?= $editProgram['type'] == 'Powerbuilding' ? 'selected' : '' ?>>Powerbuilding</option>
                    <option value="Home-based" <?= $editProgram['type'] == 'Home-based' ? 'selected' : '' ?>>Home-based</option>
                    <option value="Stretching" <?= $editProgram['type'] == 'Stretching' ? 'selected' : '' ?>>Stretching</option>
                    <option value="CrossFit" <?= $editProgram['type'] == 'CrossFit' ? 'selected' : '' ?>>CrossFit</option>
                    <option value="Pilates" <?= $editProgram['type'] == 'Pilates' ? 'selected' : '' ?>>Pilates</option>
                    <option value="Yoga" <?= $editProgram['type'] == 'Yoga' ? 'selected' : '' ?>>Yoga</option>
                </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Level</label>
              <select name="level" class="form-select" required>
                  <option value="" disabled <?= $editProgram['level'] == '' ? 'selected' : '' ?>>Select level</option>
                  <option value="Beginner" <?= $editProgram['level'] == 'Beginner' ? 'selected' : '' ?>>Beginner</option>
                  <option value="Intermediate" <?= $editProgram['level'] == 'Intermediate' ? 'selected' : '' ?>>Intermediate</option>
                  <option value="Expert" <?= $editProgram['level'] == 'Expert' ? 'selected' : '' ?>>Expert</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Duration (weeks)</label>
              <input type="text" name="duration" id="duration" class="form-control" placeholder="e.g. 6 or 6-12" value="<?= htmlspecialchars($editProgram['duration_weeks']) ?>" required>

            </div>

            <div class="col-md-6">
              <label class="form-label">Days per Week</label>
              <input type="number" name="days_per_week" class="form-control" min="1" max="7" value="<?= htmlspecialchars($editProgram['days_per_week']) ?>" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Target Group</label>
              <select name="target_group" class="form-select" required>
                  <option value="" disabled <?= $editProgram['target_group'] == '' ? 'selected' : '' ?>>Select target group</option>
                  <option value="Men" <?= $editProgram['target_group'] == 'Men' ? 'selected' : '' ?>>Men</option>
                  <option value="Women" <?= $editProgram['target_group'] == 'Women' ? 'selected' : '' ?>>Women</option>
                  <option value="All" <?= $editProgram['target_group'] == 'All' ? 'selected' : '' ?>>All</option>
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <label>Muscle Category</label>
              <div id="edit_category_buttons" class="d-flex flex-wrap gap-2"></div>
              <div id="edit_category_order_display" class="mt-2 text-muted small"></div>
              <input type="hidden" name="muscle_category" id="edit_category_hidden">
          </div>

          <div class="col-md-6 mb-3">
              <label>Muscle Groups</label>
              <div id="edit_muscle_group_buttons" class="d-flex flex-wrap gap-2"></div>
              <div id="edit_muscle_order_display" class="mt-2 text-muted small"></div>
              <input type="hidden" name="muscle_group" id="edit_muscle_group_hidden">
          </div>

            <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($editProgram['description']) ?></textarea>
            </div>

            <div class="col-12">
                <label for="tips" class="form-label">Coach's Tips</label>
                <textarea name="tips" id="tips" class="form-control" rows="3" placeholder="E.g., Start light. Focus on form. Rest between sets."><?= htmlspecialchars($editProgram['tips']) ?></textarea>
            </div>


            <div class="col-md-6">
              <label class="form-label">Image</label>
              <input type="file" name="image" class="form-control">
              <?php if (!empty($editProgram['image'])): ?>
                <div class="mt-2">
                  <img src="<?= htmlspecialchars($editProgram['image']) ?>" style="height: 80px;">
                </div>
                <input type="hidden" name="existing_image" value="<?= htmlspecialchars($editProgram['image']) ?>">
              <?php endif; ?>
            </div>

            <div class="mt-5">
              <h5>Plan Days and Exercises</h5>
              <div id="days-container"></div>
              <button type="button" class="btn btn-secondary my-3" onclick="addDay()">+ Add Day</button>
            </div>


            <input type="hidden" name="days_exercises_json" id="edit_days_exercises_json">





        </div>

        <button type="submit" class="btn btn-success mt-3">Save Changes</button>
        <a href="create_workout_program.php" class="btn btn-secondary mt-3">Cancel</a>
    </form>
  </div>
</div>
<?php endif; ?>




<h3 class="mt-5 mb-3">All Workout Programs</h3>
<div class="bg-white p-3 shadow-sm rounded">
    <?php
    $programs = $conn->query("SELECT * FROM workout_programs ORDER BY id DESC");
    ?>

    <div class="row fw-bold border-bottom pb-2 mb-2">
        <div class="col-1">ID</div>
        <div class="col-2">Title</div>
        <div class="col-1">Goal</div>
        <div class="col-1">Type</div>
        <div class="col-1">Level</div>
        <div class="col-1">Target</div>
        <div class="col-2">Muscles</div>
        <div class="col-1">Image</div>
        <div class="col-2">Actions</div>
    </div>

    <?php while ($prog = $programs->fetch_assoc()): ?>
    <div class="row align-items-center border-bottom py-2">
        <div class="col-1"><?= $prog['id'] ?></div>
        <div class="col-2"><?= htmlspecialchars($prog['title']) ?></div>
        <div class="col-1"><?= htmlspecialchars($prog['goal']) ?></div>
        <div class="col-1"><?= htmlspecialchars($prog['type']) ?></div>
        <div class="col-1"><?= htmlspecialchars($prog['level']) ?></div>
        <div class="col-1"><?= htmlspecialchars($prog['target_group']) ?></div>
        <div class="col-2">
            <?= htmlspecialchars($prog['muscle_categories']) ?><br>
            <small class="text-muted"><?= htmlspecialchars($prog['muscle_groups']) ?></small>
        </div>
        <div class="col-1">
            <?php if ($prog['image']): ?>
                <img src="<?= htmlspecialchars($prog['image']) ?>" style="height: 40px;">
            <?php endif; ?>
        </div>
        <div class="col-2 d-flex gap-2">
            <a href="?edit=<?= $prog['id'] ?>" class="btn btn-sm btn-info text-white">
              <i class="bi bi-pencil-square"></i>
            </a>

            <a href="?delete=<?= $prog['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this program?')">
                <i class="bi bi-trash"></i>
            </a>
        </div>
    </div>
    <?php endwhile; ?>
</div>

</div>

<script>
const editDaysJson = <?= json_encode(json_decode($editProgram['days_json'] ?? '[]')) ?>;
</script>

<script>
const categoriesList = ["arms", "legs", "back", "chest", "abs"];
const muscleOptions = {
    arms: ["Biceps", "Triceps", "Shoulders", "Forearms"],
    legs: ["Glutes", "Quadriceps", "Calves", "Hamstrings", "Adductors"],
    back: ["Lats", "Middle/Lower Trapezius", "Teres major", "Lower Back", "Upper Trapezius"],
    chest: ["Chest Muscles"],
    abs: ["Abdominal Muscles"]
};

let selectedCategories = [];
let selectedMuscles = [];

function setupButtonSelector(containerId, hiddenId, itemsList, selectedArray, displayId, onChange = null) {
  const container = document.getElementById(containerId);
  const hidden = document.getElementById(hiddenId);
  const display = document.getElementById(displayId);

  const render = () => {
    container.innerHTML = '';
    itemsList.forEach(item => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = selectedArray.includes(item) ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-outline-secondary';
      btn.textContent = item.charAt(0).toUpperCase() + item.slice(1);
      btn.onclick = () => {
        const i = selectedArray.indexOf(item);
        if (i === -1) selectedArray.push(item);
        else selectedArray.splice(i, 1);
        hidden.value = selectedArray
          .map(v => v.trim())
          .filter(v => v !== '')
          .join(',');


        render();
        if (onChange) onChange(selectedArray);
      };
      container.appendChild(btn);
    });
    display.textContent = "Selected: " + selectedArray.join(" → ");
  };

  render();
}

function updateAvailableMuscles(selectedCats) {
  const allMuscles = [];
  selectedCats.forEach(cat => {
    if (muscleOptions[cat]) {
      muscleOptions[cat].forEach(muscle => {
        if (!allMuscles.includes(muscle)) allMuscles.push(muscle);
      });
    }
  });
  setupButtonSelector("muscle_group_buttons", "muscle_group_hidden", allMuscles, selectedMuscles, "muscle_order_display");
}

window.addEventListener('DOMContentLoaded', () => {
  setupButtonSelector("category_buttons", "category_hidden", categoriesList, selectedCategories, "category_order_display", updateAvailableMuscles);
});
</script>






<script>
const allExercises = <?php
  $exercises = $conn->query("SELECT id, name, muscle_group FROM exercises");
  $data = [];
  while ($ex = $exercises->fetch_assoc()) {
    $data[] = $ex;
  }
  echo json_encode($data);
?>;

const allMuscles = [...new Set(
  allExercises
    .flatMap(e => e.muscle_group.split(',').map(m => m.trim()))
    .filter(m => m !== "")
)];


function addDay() {
  // Определяем, какая форма активна
  const activeForm = document.querySelector('.form-section.active');
  if (!activeForm) return;

  const container = activeForm.querySelector("#days-container");
  const template = document.getElementById("day-template");
  const dayNode = template.content.cloneNode(true);
  container.appendChild(dayNode);
}


document.addEventListener("click", function (e) {
  if (e.target.classList.contains("add-exercise-btn")) {
    const exerciseList = e.target.previousElementSibling;
    const template = document.getElementById("exercise-template");
    const exerciseNode = template.content.cloneNode(true);

    const muscleSelect = exerciseNode.querySelector(".exercise-muscle-filter");
    const exerciseSelect = exerciseNode.querySelector(".exercise-select");

    allMuscles.forEach(m => {
      const opt = document.createElement("option");
      opt.value = m;
      opt.textContent = m;
      muscleSelect.appendChild(opt);
    });

    muscleSelect.addEventListener("change", () => {
      const selected = muscleSelect.value.toLowerCase();
      exerciseSelect.innerHTML = '<option value="">Select exercise</option>';
      allExercises.forEach(ex => {
        const muscles = ex.muscle_group.toLowerCase().split(',').map(m => m.trim());
        if (muscles.includes(selected)) {
          const opt = document.createElement("option");
          opt.value = ex.id;
          opt.textContent = ex.name;
          exerciseSelect.appendChild(opt);
        }
      });
    });

    exerciseList.appendChild(exerciseNode);
  }

  if (e.target.classList.contains("remove-exercise-btn")) {
    e.target.closest(".exercise-row").remove();
  }

  if (e.target.classList.contains("remove-day-btn")) {
    e.target.closest(".day-item").remove();
  }
});

function saveDaysExercises() {
  const days = [];
  document.querySelectorAll(".day-item").forEach(day => {
    const title = day.querySelector(".day-title").value;
    const exercises = [];

    day.querySelectorAll(".exercise-row").forEach(row => {
      const id = row.querySelector(".exercise-select").value;
      const sets = row.querySelector(".sets").value;
      const reps = row.querySelector(".reps").value;
      const muscle = row.querySelector(".exercise-muscle-filter").value;
      exercises.push({ exercise_id: id, sets, reps, muscle });
    });


    days.push({ title, exercises });
  });
  document.getElementById("days_exercises_json").value = JSON.stringify(days);
  return true;
}
</script>

<script>
function toggleForm(id) {
    const createForm = document.getElementById("createForm");
    const editForm = document.querySelector(".edit-form-container");

    if (id === 'createForm') {
        if (editForm) editForm.classList.remove("active");
        createForm.classList.toggle("active");
    }

    if (id === 'editForm' && editForm) {
        createForm.classList.remove("active");
        editForm.classList.toggle("active");
    }
}
</script>


<script>
const editSelectedCategories = <?= json_encode(explode(',', $editProgram['muscle_categories'] ?? '')) ?>;
const editSelectedMuscles = <?= json_encode(explode(',', $editProgram['muscle_groups'] ?? '')) ?>;

function setupEditButtonSelector(containerId, hiddenId, itemsList, selectedArray, displayId, onChange = null) {
  const container = document.getElementById(containerId);
  const hidden = document.getElementById(hiddenId);
  const display = document.getElementById(displayId);

  const render = () => {
    container.innerHTML = '';
    itemsList.forEach(item => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = selectedArray.includes(item) ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-outline-secondary';
      btn.textContent = item.charAt(0).toUpperCase() + item.slice(1);
      btn.onclick = () => {
        const i = selectedArray.indexOf(item);
        if (i === -1) selectedArray.push(item);
        else selectedArray.splice(i, 1);
        hidden.value = selectedArray
          .map(v => v.trim())
          .filter(v => v !== '')
          .join(',');


        render();
        if (onChange) onChange(selectedArray);
      };
      container.appendChild(btn);
    });
    display.textContent = "Selected: " + selectedArray.join(" → ");
  };

  render();
}

function updateEditAvailableMuscles(selectedCats) {
  const allMuscles = [];
  selectedCats.forEach(cat => {
    if (muscleOptions[cat]) {
      muscleOptions[cat].forEach(muscle => {
        if (!allMuscles.includes(muscle)) allMuscles.push(muscle);
      });
    }
  });
  setupEditButtonSelector("edit_muscle_group_buttons", "edit_muscle_group_hidden", allMuscles, editSelectedMuscles, "edit_muscle_order_display");
}

window.addEventListener('DOMContentLoaded', () => {
  setupEditButtonSelector(
    "edit_category_buttons",
    "edit_category_hidden",
    categoriesList,
    editSelectedCategories,
    "edit_category_order_display",
    updateEditAvailableMuscles
  );

  updateEditAvailableMuscles(editSelectedCategories);

  if (editSelectedMuscles.length > 0) {
  const allMuscles = Object.values(muscleOptions).flat();
  setupEditButtonSelector(
    "edit_muscle_group_buttons",
    "edit_muscle_group_hidden",
    allMuscles,
    editSelectedMuscles,
    "edit_muscle_order_display"
  );
}


  // Сразу записываем значения в hidden-поля
  document.getElementById('edit_category_hidden').value = editSelectedCategories.map(v => v.trim()).filter(v => v !== '').join(',');
  document.getElementById('edit_muscle_group_hidden').value = editSelectedMuscles.map(v => v.trim()).filter(v => v !== '').join(',');

  const editForm = document.querySelector('#editForm form');
  if (editForm) {
    editForm.addEventListener('submit', function () {
      document.getElementById('edit_category_hidden').value = editSelectedCategories.map(v => v.trim()).filter(v => v !== '').join(',');
      document.getElementById('edit_muscle_group_hidden').value = editSelectedMuscles.map(v => v.trim()).filter(v => v !== '').join(',');

      const days = [];
      editForm.querySelectorAll(".day-item").forEach(day => {
        const title = day.querySelector(".day-title").value;
        const exercises = [];

        day.querySelectorAll(".exercise-row").forEach(row => {
          const id = row.querySelector(".exercise-select").value;
          const sets = row.querySelector(".sets").value;
          const reps = row.querySelector(".reps").value;
          const muscle = row.querySelector(".exercise-muscle-filter").value;
          exercises.push({ exercise_id: id, sets, reps, muscle });
        });


        days.push({ title, exercises });
      });
      document.getElementById("edit_days_exercises_json").value = JSON.stringify(days);
    });
  }






    if (editDaysJson.length > 0) {
      const container = document.querySelector('#editForm #days-container');
      const dayTemplate = document.getElementById("day-template");
      const exerciseTemplate = document.getElementById("exercise-template");

      editDaysJson.forEach(day => {
        const dayNode = dayTemplate.content.cloneNode(true);
        dayNode.querySelector('.day-title').value = day.title;

        const exerciseList = dayNode.querySelector('.exercise-list');

        day.exercises.forEach(ex => {
        const exNode = exerciseTemplate.content.cloneNode(true);

        const muscleSelect = exNode.querySelector('.exercise-muscle-filter');
        const exerciseSelect = exNode.querySelector('.exercise-select');
        const setsInput = exNode.querySelector('.sets');
        const repsInput = exNode.querySelector('.reps');

        // 1. Добавляем мышцы
        allMuscles.forEach(m => {
          const opt = document.createElement("option");
          opt.value = m;
          opt.textContent = m;
          muscleSelect.appendChild(opt);
        });

        // 2. Навешиваем обработчик до установки значения
        muscleSelect.addEventListener("change", () => {
          const selected = muscleSelect.value.toLowerCase();
          exerciseSelect.innerHTML = '<option value="">Select exercise</option>';
          allExercises.forEach(e => {
            const muscles = e.muscle_group.toLowerCase().split(',').map(m => m.trim());
            if (muscles.includes(selected)) {
              const opt = document.createElement("option");
              opt.value = e.id;
              opt.textContent = e.name;
              exerciseSelect.appendChild(opt);
            }
          });
        });

        // 3. Устанавливаем мышцу и данные
        muscleSelect.value = ex.muscle || '';
        setsInput.value = ex.sets;
        repsInput.value = ex.reps;

        // 4. Вызываем событие изменения
        muscleSelect.dispatchEvent(new Event("change"));

        // 5. После фильтрации — выставляем упражнение
        setTimeout(() => {
          exerciseSelect.value = ex.exercise_id;
        }, 50);

        exerciseList.appendChild(exNode);
      });


        container.appendChild(dayNode);
      });
    }





});


</script>


</body>
</html>
