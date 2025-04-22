<?php
$conn = new mysqli("localhost", "root", "", "gymbridges");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
mysqli_set_charset($conn, "utf8mb4");

$edit_mode = false;
$edit_data = null;

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM exercises WHERE id = $id");
    header("Location: exercises_list.php");
    exit();
}

if (isset($_GET['edit'])) {
    $edit_mode = true;
    $edit_id = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM exercises WHERE id = $edit_id");
    if ($res && $res->num_rows > 0) {
        $edit_data = $res->fetch_assoc();
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['edit_submit'])) {
    $id = intval($_POST['edit_id']);

    $imagePath = $conn->query("SELECT image FROM exercises WHERE id = $id")->fetch_assoc()['image'] ?? '';

    if (isset($_POST['remove_image']) && $imagePath && file_exists($imagePath)) {
        unlink($imagePath);
        $imagePath = '';
    }

    if (!empty($_FILES['new_image']['name'])) {
        $target_dir = "uploads/";
        $imagePath = $target_dir . basename($_FILES["new_image"]["name"]);
        move_uploaded_file($_FILES["new_image"]["tmp_name"], $imagePath);
    }

    $stmt = $conn->prepare("UPDATE exercises SET name=?, description=?, video_url=?, category=?, muscle_group=?, equipment=?, difficulty=?, image=? WHERE id=?");
   $stmt->bind_param("ssssssisi",
    $_POST['name'],
    $_POST['description'],
    $_POST['video_url'],
    $_POST['category'],
    $_POST['muscle_group'],
    $_POST['equipment'],
    $_POST['difficulty'],
    $imagePath,
    $id
);

    $stmt->execute();
    $stmt->close();

    header("Location: exercises_list.php");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['create_submit'])) {
    $image = '';
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        $image = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image);
    }

    $stmt = $conn->prepare("INSERT INTO exercises (name, description, video_url, image, category, muscle_group, equipment, difficulty) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssi", $_POST['name'], $_POST['description'], $_POST['video_url'], $image, $_POST['category'], $_POST['muscle_group'], $_POST['equipment'], $_POST['difficulty']);
    $stmt->execute();
    $stmt->close();
    header("Location: exercises_list.php");
    exit();
}

$exercises = $conn->query("SELECT * FROM exercises ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exercises</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .form-section { display: none; }
        .form-section.active { display: block; }
    </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Exercise List</h2>
    <button class="btn btn-primary mb-3" onclick="toggleForm('createForm')">
        <i class="bi bi-plus-circle"></i> Add Exercise
    </button>


    <!-- Create Form -->
    <div id="createForm" class="form-section">
        <form method="POST" enctype="multipart/form-data" class="card p-4 mb-4 shadow-sm bg-white">
            <h4>Create Exercise</h4>
            <div class="row">
                <div class="col-md-6 mb-3"><label>Name</label><input name="name" class="form-control" required></div>
                <div class="col-md-6 mb-3"><label>Video URL</label><input name="video_url" class="form-control"></div>
                <div class="col-md-6 mb-3"><label>Image</label><input type="file" name="image" class="form-control"></div>
                <div class="col-md-6 mb-3"><label>Equipment</label><input name="equipment" class="form-control"></div>
                <div class="col-md-6 mb-3"><label>Category</label>
                    <select name="category" class="form-select" onchange="updateMuscles(this.value, null)" id="category_create" required>
                        <option value="">Select</option><option value="arms">Arms</option><option value="legs">Legs</option>
                        <option value="back">Back</option><option value="chest">Chest</option><option value="abs">Abs</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3"><label>Muscle Group</label>
                    <select name="muscle_group" class="form-select" id="muscle_group_create" required></select>
                </div>
                <div class="col-12 mb-3"><label>Description</label><textarea name="description" class="form-control" required></textarea></div>
                <div class="col-12 mb-3"><label>Difficulty</label><input type="number" name="difficulty" class="form-control" min="1" max="5" required></div>
            </div>
            <button type="submit" name="create_submit" class="btn btn-success">Save</button>
        </form>
    </div>

    <!-- Edit Form -->
    <div id="editForm" class="form-section">
        <form method="POST" enctype="multipart/form-data" class="card p-4 mb-4 shadow-sm bg-white">
            <input type="hidden" name="edit_id">
            <h4>Edit Exercise</h4>
            <div class="row">
                <div class="col-md-6 mb-3"><label>Name</label><input name="name" class="form-control" required></div>
                <div class="col-md-6 mb-3"><label>Video URL</label><input name="video_url" class="form-control"></div>
                <div class="col-md-6 mb-3"><label>Equipment</label><input name="equipment" class="form-control"></div>
                <div class="col-md-6 mb-3"><label>Category</label>
                    <select name="category" class="form-select" id="category_edit" required onchange="updateMuscles(this.value, 'muscle_group_edit')">
                        <option value="">Select</option>
                        <option value="arms">Arms</option>
                        <option value="legs">Legs</option>
                        <option value="back">Back</option>
                        <option value="chest">Chest</option>
                        <option value="abs">Abs</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3"><label>Muscle Group</label>
                    <select name="muscle_group" class="form-select" id="muscle_group_edit" required></select>
                </div>
                <div class="col-12 mb-3"><label>Description</label><textarea name="description" class="form-control" required></textarea></div>
                <div class="col-12 mb-3"><label>Difficulty</label><input type="number" name="difficulty" class="form-control" min="1" max="5" required></div>
                <div class="col-md-6 mb-3">
                    <label>Current Image</label><br>
                    <img id="current-image-preview" src="" style="max-height: 100px; display: none;"><br>
                    <input type="checkbox" name="remove_image" value="1" id="remove_image_checkbox" style="display: none">
                    <label for="remove_image_checkbox" style="display: none">Remove</label>
                </div>


                <div class="col-md-6 mb-3">
                    <label>New Image (optional)</label>
                    <input type="file" name="new_image" class="form-control">
                </div>
            </div>
            <button type="submit" name="edit_submit" class="btn btn-primary">Update</button>
        </form>
    </div>


    <div class="bg-white p-3 shadow-sm rounded">
        <div class="row fw-bold border-bottom pb-2 mb-2">
            <div class="col-1">ID</div>
            <div class="col-2">Name</div>
            <div class="col-1">Category</div>
            <div class="col-2">Muscle</div>
            <div class="col-1">Equip.</div>
            <div class="col-1">Diff</div>
            <div class="col-1">Video</div>
            <div class="col-1">Image</div>
            <div class="col-2">Actions</div>
        </div>
        <?php while ($row = $exercises->fetch_assoc()): ?>
            <div class="row align-items-center border-bottom py-2">
                <div class="col-1"><?= $row['id'] ?></div>
                <div class="col-2"><?= htmlspecialchars($row['name']) ?></div>
                <div class="col-1"><?= htmlspecialchars($row['category']) ?></div>
                <div class="col-2"><?= htmlspecialchars($row['muscle_group']) ?></div>
                <div class="col-1"><?= htmlspecialchars($row['equipment']) ?></div>
                <div class="col-1"><?= $row['difficulty'] ?></div>
                <div class="col-1">
                    <?php if ($row['video_url']): ?>
                        <a href="<?= htmlspecialchars($row['video_url']) ?>" target="_blank">
                            <i class="bi bi-play-btn"></i>
                        </a>
                    <?php endif; ?>
                </div>
                <div class="col-1">
                    <?php if ($row['image']): ?>
                        <img src="<?= htmlspecialchars($row['image']) ?>" height="30">
                    <?php endif; ?>
                </div>
                <div class="col-2">
                <button class="btn btn-sm btn-info text-white edit-btn"
                    data-id="<?= $row['id'] ?>"
                    data-name="<?= htmlspecialchars($row['name']) ?>"
                    data-video="<?= htmlspecialchars($row['video_url']) ?>"
                    data-equipment="<?= htmlspecialchars($row['equipment']) ?>"
                    data-category="<?= htmlspecialchars($row['category']) ?>"
                    data-muscle="<?= htmlspecialchars($row['muscle_group']) ?>"
                    data-description="<?= htmlspecialchars($row['description']) ?>"
                    data-difficulty="<?= $row['difficulty'] ?>"
                    data-image="<?= htmlspecialchars($row['image']) ?>">
                    <i class="bi bi-pencil-square"></i>
                </button>




                <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">
                    <i class="bi bi-trash"></i>
                </a>

                </div>
            </div>
        <?php endwhile; ?>
    </div>


</div>


<script>
const muscleOptions = {
    arms: ["Biceps", "Triceps", "Shoulders", "Forearms"],
    legs: ["Glutes", "Quadriceps", "Calves", "Hamstrings", "Adductors"],
    back: ["Back Muscles", "Trapezius", "Lats"],
    chest: ["Chest Muscles"],
    abs: ["Abdominal Muscles"]
};

// Универсальная функция обновления списка мышц
function updateMuscles(category, selectId, selected = null) {
    const select = document.getElementById(selectId);
    if (!select) return;

    select.innerHTML = "";
    if (muscleOptions[category]) {
        muscleOptions[category].forEach(muscle => {
            const opt = new Option(muscle, muscle);
            if (selected && muscle === selected) opt.selected = true;
            select.appendChild(opt);
        });
    }
}
</script>

<script>
let currentEditId = null;

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".edit-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            const id = btn.dataset.id;
            const form = document.getElementById("editForm");
            const createForm = document.getElementById("createForm");

            // Повторное нажатие по той же записи — закрытие формы
            if (currentEditId === id && form.classList.contains("active")) {
                form.classList.remove("active");
                currentEditId = null;
                return;
            }

            createForm.classList.remove("active");
            form.classList.add("active");
            currentEditId = id;

            // Заполняем поля
            form.querySelector("input[name='edit_id']").value = id;
            form.querySelector("input[name='name']").value = btn.dataset.name;
            form.querySelector("input[name='video_url']").value = btn.dataset.video;
            form.querySelector("input[name='equipment']").value = btn.dataset.equipment;
            form.querySelector("select[name='category']").value = btn.dataset.category;
            updateMuscles(btn.dataset.category, "muscle_group_edit", btn.dataset.muscle);
            form.querySelector("textarea[name='description']").value = btn.dataset.description;
            form.querySelector("input[name='difficulty']").value = btn.dataset.difficulty;

            // 👉 Показ текущей картинки
            const imagePreview = document.getElementById("current-image-preview");
            const checkbox = document.getElementById("remove_image_checkbox");
            const checkboxLabel = document.querySelector("label[for='remove_image_checkbox']");

            if (btn.dataset.image) {
                imagePreview.src = btn.dataset.image;
                imagePreview.style.display = "block";
                checkbox.style.display = "inline";
                checkboxLabel.style.display = "inline";
            } else {
                imagePreview.src = "";
                imagePreview.style.display = "none";
                checkbox.style.display = "none";
                checkboxLabel.style.display = "none";
            }
        });
    });
});

</script>


<script>
function toggleForm(id) {
    const form = document.getElementById(id);
    form.classList.toggle("active");

    // Если открываем создание — скрываем редактирование
    if (id === "createForm") {
        const editForm = document.getElementById("editForm");
        if (editForm && editForm.classList.contains("active")) {
            editForm.classList.remove("active");
        }
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
