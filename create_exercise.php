<?php
// Подключение к базе
$conn = new mysqli("localhost", "root", "", "gymbridges");
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Обработка формы
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $video_url = $_POST['video_url'];
    $category = $_POST['category'];
    $muscle_group = $_POST['muscle_group'];
    $equipment = $_POST['equipment'];
    $difficulty = (int)$_POST['difficulty'];

    // Обработка загрузки изображения
    $image = '';
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        $image = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image);
    }

    $stmt = $conn->prepare("INSERT INTO exercises (name, description, video_url, image, category, muscle_group, equipment, difficulty) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssi", $name, $description, $video_url, $image, $category, $muscle_group, $equipment, $difficulty);
    $stmt->execute();
    $stmt->close();

    echo "<div class='alert alert-success'>Exercise added successfully!</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Exercise</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script>
    const muscleOptions = {
      arms: ["Biceps", "Triceps", "Shoulders", "Forearms"],
      legs: ["Glutes", "Quadriceps", "Calves", "Hamstrings", "Adductors"],
      back: ["Back Muscles", "Trapezius", "Lats"],
      chest: ["Chest Muscles"],
      abs: ["Abdominal Muscles"]
    };

    function updateMuscles() {
      const category = document.getElementById("category").value;
      const muscleSelect = document.getElementById("muscle_group");

      muscleSelect.innerHTML = "";

      if (muscleOptions[category]) {
        muscleOptions[category].forEach(muscle => {
          const option = document.createElement("option");
          option.value = muscle;
          option.textContent = muscle;
          muscleSelect.appendChild(option);
        });
      }
    }
  </script>
</head>
<body class="bg-light">
  <div class="container mt-5">
    <h2 class="mb-4">Create New Exercise</h2>

    <form method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm bg-white">
      <div class="mb-3">
        <label class="form-label">Exercise Name</label>
        <input type="text" name="name" class="form-control" required maxlength="100">
      </div>

      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="4" required></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Video URL</label>
        <input type="url" name="video_url" class="form-control" maxlength="255">
      </div>

      <div class="mb-3">
        <label class="form-label">Image</label>
        <input type="file" name="image" class="form-control">
      </div>

      <div class="mb-3">
        <label class="form-label">Category</label>
        <select name="category" id="category" class="form-select" onchange="updateMuscles()" required>
          <option value="">Select category</option>
          <option value="arms">Arms</option>
          <option value="legs">Legs</option>
          <option value="back">Back</option>
          <option value="chest">Chest</option>
          <option value="abs">Abs</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Muscle Group</label>
        <select name="muscle_group" id="muscle_group" class="form-select" required></select>
      </div>

      <div class="mb-3">
        <label class="form-label">Equipment</label>
        <input type="text" name="equipment" class="form-control" maxlength="100">
      </div>

      <div class="mb-3">
        <label class="form-label">Difficulty Level (1-5)</label>
        <input type="number" name="difficulty" class="form-control" min="1" max="5" required>
      </div>

      <button type="submit" class="btn btn-primary">Add Exercise</button>
    </form>
  </div>
</body>
</html>
