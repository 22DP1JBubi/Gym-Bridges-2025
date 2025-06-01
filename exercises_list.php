<?php

session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'superadmin'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'] ?? 'Admin';


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

    $muscle_group = $_POST['muscle_group'] ?? '';
    $equipment = $_POST['equipment'] ?? '';
    $category = $_POST['category'] ?? '';
    

    
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

    $muscle_group = $_POST['muscle_group'] ?? '';
    $equipment = $_POST['equipment'] ?? '';
    $category = $_POST['category'] ?? '';


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
<?php include 'includes/admin_sidebar.php'; ?>
<div class="main-content" style="margin-left: 220px; padding: 30px;">
    <h2 class="mb-4">Exercise List</h2>
    <button class="btn btn-primary mb-3" onclick="toggleForm('createForm')">
        <i class="bi bi-plus-circle"></i> Add Exercise
    </button>


    <!-- Create Form -->
    <div id="createForm" class="form-section">
        <form method="POST" enctype="multipart/form-data" class="card p-4 mb-4 shadow-sm bg-white" onsubmit="return prepareMuscles();">
            <h4>Create Exercise</h4>
            <div class="row">
                <div class="col-md-6 mb-3"><label>Name</label><input name="name" class="form-control" required></div>
                <div class="col-md-6 mb-3"><label>Video URL</label><input name="video_url" class="form-control"></div>
                <div class="col-md-6 mb-3"><label>Image</label><input type="file" name="image" class="form-control">
                    <div class="form-text text-muted">
                        Max 20 images, total size must not exceed 40MB.
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Equipment</label>
                    <div id="equipment_buttons" class="d-flex flex-wrap gap-2"></div>

                    <div id="equipment_order_display" class="mt-2 text-muted small"></div>

                    <input type="hidden" name="equipment" id="equipment_hidden">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Category</label>
                    <div id="category_buttons" class="d-flex flex-wrap gap-2"></div>

                    <div id="category_order_display" class="mt-2 text-muted small"></div>

                    <input type="hidden" name="category" id="category_hidden">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Muscle Group</label>
                    <div id="muscle_group_buttons_create" class="d-flex flex-wrap gap-2"></div>

                    <div id="muscle_order_display" class="mt-2 text-muted small"></div>

                    <input type="hidden" name="muscle_group" id="muscle_group_hidden_create">
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
        <form method="POST" enctype="multipart/form-data" class="card p-4 mb-4 shadow-sm bg-white" onsubmit="return prepareEditForm();">
            <input type="hidden" name="edit_id">
            <h4>Edit Exercise</h4>
            <div class="row">
                <div class="col-md-6 mb-3"><label>Name</label><input name="name" class="form-control" required></div>
                <div class="col-md-6 mb-3"><label>Video URL</label><input name="video_url" class="form-control"></div>

                <div class="col-md-6 mb-3">
                    <label>Equipment</label>
                    <div id="equipment_buttons_edit" class="d-flex flex-wrap gap-2"></div>
                    <input type="hidden" name="equipment" id="equipment_hidden_edit">
                    <div class="small text-muted mt-1" id="equipment_order_edit"></div>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Category</label>
                    <div id="category_buttons_edit" class="d-flex flex-wrap gap-2"></div>
                    <input type="hidden" name="category" id="category_hidden_edit">
                    <div class="small text-muted mt-1" id="category_order_edit"></div>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Muscle Group</label>
                    <div id="muscle_group_buttons_edit" class="d-flex flex-wrap gap-2"></div>
                    <input type="hidden" name="muscle_group" id="muscle_group_hidden_edit">
                    <div class="small text-muted mt-1" id="muscle_order_edit"></div>
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
                    <div class="form-text text-muted">
                        Max 20 images, total size must not exceed 40MB.
                    </div>
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
        </div> <!-- col-md-10 -->
    </div> <!-- row -->
</div> <!-- container-fluid -->


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script>
const categoriesList = ["arms", "legs", "back", "chest", "abs"];
const equipmentList = [
    "Barbell", "EZ-bar", "Straight bar", "Dumbbells", "Pull-up bar", "Dip bars", "Kettlebell",
    "Bench", "Decline bench", "Incline bench", "Cable machine", "Smith machine", "Crossover machine", "TRX straps",
    "Medicine ball", "Weight belt", "Rope attachment", "V-bar attachment", "Single handle", "Pec deck machine", 
    "Mat", "Roman chair", "Hammer Strength Machine", "Seated Row Machine", "T-Bar attachment",
    "Machine T-Bar Row", "Shrug machine", "Hyperextension bench", " Power rack", "Squat rack",
    "Leg press machine", "Leg extension machine", "Hack squat machine", "Leg curl machine", "Adduction machine",
    "Ankle strap", "Seated Calf Machine", "Standing calf raise machine", "platform"
];

const muscleOptions = {
    arms: ["Biceps", "Triceps", "Shoulders", "Forearms"],
    legs: ["Glutes", "Quadriceps", "Calves", "Hamstrings", "Adductors"],
    back: ["Lats", "Middle/Lower Trapezius", "Teres major", "Lower Back", "Upper Trapezius"],
    chest: ["Chest Muscles"],
    abs: ["Abdominal Muscles"]
};

let selectedCategories = [];
let selectedEquipment = [];
let selectedMuscles = [];

// Универсальный генератор кнопок
function setupButtonSelector(containerId, hiddenId, itemsList, selectedArray, onChange = null) {
    const container = document.getElementById(containerId);
    const hidden = document.getElementById(hiddenId);

    const render = () => {
        container.innerHTML = '';
        itemsList.forEach(item => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = selectedArray.includes(item)
                ? 'btn btn-sm btn-primary'
                : 'btn btn-sm btn-outline-secondary';
            btn.textContent = item.charAt(0).toUpperCase() + item.slice(1);
            btn.onclick = () => {
                const i = selectedArray.indexOf(item);
                if (i === -1) selectedArray.push(item);
                else selectedArray.splice(i, 1);
                hidden.value = selectedArray.join(',');
                render();
                if (onChange) onChange(selectedArray);
            };
            container.appendChild(btn);
        });

        // ⬇️ Вставь прямо сюда в конец функции render()
        if (containerId === 'category_buttons') {
            document.getElementById("category_order_display").textContent =
                "Selected: " + selectedArray.join(" → ");
        }
        if (containerId === 'equipment_buttons') {
            document.getElementById("equipment_order_display").textContent =
                "Selected: " + selectedArray.join(" → ");
        }
    };

    render();
}


// Мышцы
function renderMuscleButtons(muscles) {
    const container = document.getElementById("muscle_group_buttons_create");
    const hidden = document.getElementById("muscle_group_hidden_create");
    container.innerHTML = '';

    muscles.forEach(muscle => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = selectedMuscles.includes(muscle)
            ? 'btn btn-sm btn-primary'
            : 'btn btn-sm btn-outline-secondary';
        btn.textContent = muscle;
        btn.onclick = () => {
            const i = selectedMuscles.indexOf(muscle);
            if (i === -1) selectedMuscles.push(muscle);
            else selectedMuscles.splice(i, 1);
            hidden.value = selectedMuscles.join(',');
            renderMuscleButtons(muscles);
        };
        container.appendChild(btn);
    });

    // 👇 Добавляем строку с текущим порядком
    document.getElementById("muscle_order_display").textContent =
        "Selected: " + selectedMuscles.join(" → ");

        
}


// Обновить список мышц
function updateAvailableMuscles(selectedCats) {
    const allMuscles = [];
    selectedCats.forEach(cat => {
        if (muscleOptions[cat]) {
            muscleOptions[cat].forEach(muscle => {
                if (!allMuscles.includes(muscle)) allMuscles.push(muscle);
            });
        }
    });
    renderMuscleButtons(allMuscles);
}

// Финальная подготовка перед отправкой
function prepareMuscles() {
    document.getElementById("muscle_group_hidden_create").value = selectedMuscles.join(',');
    document.getElementById("category_hidden").value = selectedCategories.join(',');
    document.getElementById("equipment_hidden").value = selectedEquipment.join(',');
    return true;
}

// Активация при загрузке
document.addEventListener("DOMContentLoaded", () => {
    setupButtonSelector("category_buttons", "category_hidden", categoriesList, selectedCategories, updateAvailableMuscles);
    setupButtonSelector("equipment_buttons", "equipment_hidden", equipmentList, selectedEquipment);
});
</script>


<script>
// 🔁 Функция переключения отображения формы
function toggleForm(id) {
    const form = document.getElementById(id);
    form.classList.toggle("active");

    // Скрываем вторую форму, если открыта
    if (id === "createForm") {
        const editForm = document.getElementById("editForm");
        if (editForm && editForm.classList.contains("active")) {
            editForm.classList.remove("active");
        }
    }
}
</script>


<script>
// ⚙️ Вставляется после загрузки DOM и определения setupButtonSelector и muscleOptions

function openEditForm(data) {
    const form = document.getElementById("editForm");
    const createForm = document.getElementById("createForm");
    createForm.classList.remove("active");
    form.classList.add("active");

    // Установить значения полей
    form.querySelector("input[name='edit_id']").value = data.id;
    form.querySelector("input[name='name']").value = data.name;
    form.querySelector("input[name='video_url']").value = data.video;
    form.querySelector("textarea[name='description']").value = data.description;
    form.querySelector("textarea[name='instruction']").value = data.instruction || '';
    form.querySelector("input[name='difficulty']").value = data.difficulty;

    // 🧩 Разбиваем и очищаем
    const categories = data.category ? data.category.split(',').map(s => s.trim()) : [];
    const equipment = data.equipment ? data.equipment.split(',').map(s => s.trim()) : [];
    const muscles = data.muscle ? data.muscle.split(',').map(s => s.trim()) : [];

    selectedCategories_edit = [...categories];
    selectedEquipment_edit = [...equipment];
    selectedMuscles_edit = [...muscles];

    document.getElementById("category_hidden_edit").value = selectedCategories_edit.join(',');
    document.getElementById("equipment_hidden_edit").value = selectedEquipment_edit.join(',');
    document.getElementById("muscle_group_hidden_edit").value = selectedMuscles_edit.join(',');

    updateAvailableMuscles_edit(selectedCategories_edit);
    renderAllEditButtons();

    // Картинка
    const img = document.getElementById("current-image-preview");
    const checkbox = document.getElementById("remove_image_checkbox");
    const checkboxLabel = document.querySelector("label[for='remove_image_checkbox']");

    if (data.image) {
        img.src = data.image;
        img.style.display = "block";
        checkbox.style.display = "inline";
        checkboxLabel.style.display = "inline";
    } else {
        img.src = "";
        img.style.display = "none";
        checkbox.style.display = "none";
        checkboxLabel.style.display = "none";
    }
}

function renderAllEditButtons() {
    renderEditCategoryButtons();
    renderEditEquipmentButtons();
    renderEditMuscleButtons();
    updateEditOrderLabels();
}

function renderEditCategoryButtons() {
    const container = document.getElementById("category_buttons_edit");
    const hidden = document.getElementById("category_hidden_edit");
    container.innerHTML = '';

    categoriesList.forEach(item => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = selectedCategories_edit.includes(item) ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-outline-secondary';
        btn.textContent = item.charAt(0).toUpperCase() + item.slice(1);
        btn.onclick = () => {
            const i = selectedCategories_edit.indexOf(item);
            if (i === -1) selectedCategories_edit.push(item);
            else selectedCategories_edit.splice(i, 1);
            hidden.value = selectedCategories_edit.join(',');
            renderAllEditButtons();
        };
        container.appendChild(btn);
    });
}

function renderEditEquipmentButtons() {
    const container = document.getElementById("equipment_buttons_edit");
    const hidden = document.getElementById("equipment_hidden_edit");
    container.innerHTML = '';

    equipmentList.forEach(item => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = selectedEquipment_edit.includes(item) ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-outline-secondary';
        btn.textContent = item;
        btn.onclick = () => {
            const i = selectedEquipment_edit.indexOf(item);
            if (i === -1) selectedEquipment_edit.push(item);
            else selectedEquipment_edit.splice(i, 1);
            hidden.value = selectedEquipment_edit.join(',');
            renderAllEditButtons();
        };
        container.appendChild(btn);
    });
}

function renderEditMuscleButtons() {
    const container = document.getElementById("muscle_group_buttons_edit");
    const hidden = document.getElementById("muscle_group_hidden_edit");
    container.innerHTML = '';

    const allMuscles = [];
    selectedCategories_edit.forEach(cat => {
        if (muscleOptions[cat]) {
            muscleOptions[cat].forEach(muscle => {
                if (!allMuscles.includes(muscle)) allMuscles.push(muscle);
            });
        }
    });

    allMuscles.forEach(muscle => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = selectedMuscles_edit.includes(muscle) ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-outline-secondary';
        btn.textContent = muscle;
        btn.onclick = () => {
            const i = selectedMuscles_edit.indexOf(muscle);
            if (i === -1) selectedMuscles_edit.push(muscle);
            else selectedMuscles_edit.splice(i, 1);
            hidden.value = selectedMuscles_edit.join(',');
            renderEditMuscleButtons();
            updateEditOrderLabels();
        };
        container.appendChild(btn);
    });
}

function updateAvailableMuscles_edit(selectedCats) {
    renderEditMuscleButtons();
}

function updateEditOrderLabels() {
    document.getElementById("category_order_edit").textContent = selectedCategories_edit.join(', ');
    document.getElementById("equipment_order_edit").textContent = selectedEquipment_edit.join(', ');
    document.getElementById("muscle_order_edit").textContent = selectedMuscles_edit.join(', ');
}

// ОБРАБОТЧИК РЕДАКТИРОВАНИЯ
document.querySelectorAll(".edit-btn").forEach(btn => {
    btn.addEventListener("click", () => {
        openEditForm({
            id: btn.dataset.id,
            name: btn.dataset.name,
            video: btn.dataset.video,
            equipment: btn.dataset.equipment,
            category: btn.dataset.category,
            muscle: btn.dataset.muscle,
            description: btn.dataset.description,
            instruction: btn.dataset.instruction,
            difficulty: btn.dataset.difficulty,
            image: btn.dataset.image
        });
    });
});

</script>

</body>
</html>
