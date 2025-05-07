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

    // Получаем текущий путь к изображению из базы
    $imagePath = $conn->query("SELECT image FROM exercises WHERE id = $id")->fetch_assoc()['image'] ?? '';

    // Удаляем изображение, если пользователь отметил "remove"
    if (isset($_POST['remove_image']) && $imagePath && file_exists($imagePath)) {
        unlink($imagePath);
        $imagePath = '';
    }

    // Загружаем новое изображение, если выбрано
    if (!empty($_FILES['new_image']['name'])) {
        $target_dir = "uploads/";
        $imagePath = $target_dir . basename($_FILES["new_image"]["name"]);
        move_uploaded_file($_FILES["new_image"]["tmp_name"], $imagePath);
    }

    $category = isset($_POST['category']) ? implode(',', $_POST['category']) : '';
    $muscle_group = isset($_POST['muscle_group']) ? implode(',', $_POST['muscle_group']) : '';
    $equipment = isset($_POST['equipment']) ? implode(',', $_POST['equipment']) : '';
    
    $description = $_POST['description'] ?? '';
    $instruction = $_POST['instruction'] ?? '';


    // Обновляем запись
    $stmt = $conn->prepare("UPDATE exercises SET name=?, description=?, instruction=?, video_url=?, category=?, muscle_group=?, equipment=?, difficulty=?, image=? WHERE id=?");
    $stmt->bind_param("sssssssisi",
        $_POST['name'],
        $description,
        $instruction,
        $_POST['video_url'],
        $category,
        $muscle_group,
        $equipment,
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

    $muscle_group = implode(',', $_POST['muscle_group']);

    $equipment = isset($_POST['equipment']) && is_array($_POST['equipment']) ? implode(',', $_POST['equipment']): '';


    $category = implode(',', $_POST['category']);

    $description = $_POST['description'] ?? '';
    $instruction = $_POST['instruction'] ?? '';


    $stmt = $conn->prepare("INSERT INTO exercises (name, description, instruction, video_url, image, category, muscle_group, equipment, difficulty) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssi",
        $_POST['name'],
        $description,
        $instruction,
        $_POST['video_url'],
        $image,
        $category,
        $muscle_group,
        $equipment,
        $_POST['difficulty']
    );

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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


    <style>
        .form-section { display: none; }
        .form-section.active { display: block; }
    </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Exercise List123</h2>
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
                <div class="col-md-6 mb-3">
                    <label>Equipment</label>
                    <select name="equipment[]" id="equipment_create" class="form-select" multiple="multiple">
                        <option value="Barbell">Barbell</option>
                        <option value="Dumbbells">Dumbbells</option>
                        <option value="Pull-up bar">Pull-up bar</option>
                        <option value="Dip bars">Dip bars</option>
                        <option value="Bench">Bench</option>
                        <option value="Cable machine">Cable machine</option>
                        <option value="Smith machine">Smith machine</option>
                        <option value="Crossover machine">Crossover machine</option>
                        <option value="TRX straps">TRX straps</option>
                        <option value="Medicine ball">Medicine ball</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3"><label>Category</label>
                    <select name="category[]" id="category_create" class="form-select" multiple required>
                        <option value="arms">Arms</option>
                        <option value="legs">Legs</option>
                        <option value="back">Back</option>
                        <option value="chest">Chest</option>
                        <option value="abs">Abs</option>
                    </select>

                </div>
                <div class="col-md-6 mb-3"><label>Muscle Group</label>
                    <select name="muscle_group[]" class="form-select" id="muscle_group_create" multiple="multiple" required></select>
                </div>

                <div class="col-12 mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control" required></textarea>
                </div>

                <div class="col-12 mb-3">
                    <label for="instruction" class="form-label">Instruction</label>
                    <textarea class="form-control" id="instruction" name="instruction" rows="4" placeholder="Step-by-step instructions"></textarea>
                </div>

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
                <div class="col-md-6 mb-3">
                    <label>Equipment</label>
                    <select name="equipment[]" id="equipment_edit" class="form-select" multiple="multiple">
                        <option value="Barbell">Barbell</option>
                        <option value="Dumbbells">Dumbbells</option>
                        <option value="Pull-up bar">Pull-up bar</option>
                        <option value="Dip bars">Dip bars</option>
                        <option value="Bench">Bench</option>
                        <option value="Cable machine">Cable machine</option>
                        <option value="Smith machine">Smith machine</option>
                        <option value="Crossover machine">Crossover machine</option>
                        <option value="TRX straps">TRX straps</option>
                        <option value="Medicine ball">Medicine ball</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3"><label>Category</label>
                    <select name="category[]" class="form-select" id="category_edit" multiple required>

                        <option value="">Select</option>
                        <option value="arms">Arms</option>
                        <option value="legs">Legs</option>
                        <option value="back">Back</option>
                        <option value="chest">Chest</option>
                        <option value="abs">Abs</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3"><label>Muscle Group</label>
                + <select name="muscle_group[]" class="form-select" id="muscle_group_edit" multiple="multiple" required></select>
                </div>

                <div class="col-12 mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control" required></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="edit_instruction" class="form-label">Instruction</label>
                    <textarea class="form-control" id="edit_instruction" name="instruction" rows="4" placeholder="Step-by-step instructions"><?= htmlspecialchars($row['instruction']) ?></textarea>
                </div>


                
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

            <!-- Добавь в editForm перед </form> -->
            <input type="hidden" name="equipment[]" value="">
            <input type="hidden" name="category[]" value="">
            <input type="hidden" name="muscle_group[]" value="">


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
                    data-instruction="<?= htmlspecialchars($row['instruction']) ?>"
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
    back: ["Back Muscles", "Trapezius", "Lats", "Lower Back", "Teres major"],
    chest: ["Chest Muscles"],
    abs: ["Abdominal Muscles"]
};

// Универсальная функция обновления списка мышц
function updateMuscles(categoryArray, selectId, selected = []) {
    const select = document.getElementById(selectId);
    if (!select) return;

    select.innerHTML = "";

    const categories = Array.isArray(categoryArray) ? categoryArray : [categoryArray];

    const added = new Set();

    categories.forEach(category => {
        if (muscleOptions[category]) {
            muscleOptions[category].forEach(muscle => {
                if (!added.has(muscle)) {
                    const opt = new Option(muscle, muscle);
                    if (selected.includes(muscle)) opt.selected = true;
                    select.appendChild(opt);
                    added.add(muscle);
                }
            });
        }
    });

    $(`#${selectId}`).trigger('change');
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
            form.querySelector("textarea[name='description']").value = btn.dataset.description;
            form.querySelector("textarea[name='instruction']").value = btn.dataset.instruction || '';
            form.querySelector("input[name='difficulty']").value = btn.dataset.difficulty;


            const categories = btn.dataset.category.split(',').map(c => c.trim());
            $('#category_edit').val(categories).trigger('change');

            const muscles = btn.dataset.muscle.split(',').map(m => m.trim());
            updateMuscles(categories, "muscle_group_edit", muscles);

            // equipment as tags
            const equipmentField = $('#equipment_edit');
            if (btn.dataset.equipment) {
                const items = btn.dataset.equipment.split(',').map(e => e.trim());
                equipmentField.val(items).trigger('change');
            }


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

<script>
document.addEventListener("DOMContentLoaded", () => {
    $('#equipment_create').select2({
        tags: true,
        placeholder: "Select or add equipment",
        width: '100%'
    });

    $('#equipment_edit').select2({
        tags: true,
        placeholder: "Select or add equipment",
        width: '100%'
    });

    $('#category_create, #category_edit, #muscle_group_create, #muscle_group_edit').select2({
        tags: false,
        placeholder: "Select or add",
        width: '100%'
    });

    // 🧠 Вот это добавь:
    $('#category_create').on('change', function () {
        const selected = $(this).val(); // массив категорий
        updateMuscles(selected, 'muscle_group_create');
    });

    $('#category_edit').on('change', function () {
        const selected = $(this).val(); // массив категорий
        updateMuscles(selected, 'muscle_group_edit');
    });
});



</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

</body>
</html>
