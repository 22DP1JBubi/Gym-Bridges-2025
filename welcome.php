<?php
// Включаем отображение всех ошибок
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Запускаем сессию
session_start();

// Проверяем, залогинен ли пользователь
if (!isset($_SESSION['user_id'])) {
    echo "Ошибка: пользователь не авторизован.";
    exit();
}

// Подключение к БД
$conn = new mysqli('localhost', 'root', '', 'gymbridges');
if ($conn->connect_error) {
    die("Ошибка подключения к базе: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Подготовленный запрос
$stmt = $conn->prepare("SELECT * FROM users WHERE userID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();

// Проверка: нашли ли пользователя
if (!$user) {
    echo "Ошибка: пользователь с ID $user_id не найден.";
    exit();
}

$regDate = !empty($user['registrationDate']) ? $user['registrationDate'] : 'N/A';
$lastLogin = !empty($user['lastLoginDate']) ? $user['lastLoginDate'] : 'N/A';
$isPremium = isset($user['isPremium']) && $user['isPremium'] == 1;
$birthdate = !empty($user['birthdate']) ? $user['birthdate'] : 'N/A';
$height = isset($user['height']) ? htmlspecialchars($user['height']) : 'N/A';
$weight = isset($user['weight']) ? htmlspecialchars($user['weight']) : 'N/A';
$gender = isset($user['gender']) ? htmlspecialchars($user['gender']) : 'N/A';

// Автоматический расчет возраста
$age = 'N/A';
if (!empty($user['birthdate']) && $user['birthdate'] !== '0000-00-00') {
    try {
        $birth = new DateTime($user['birthdate']);
        $today = new DateTime();
        $age = $birth->diff($today)->y;
    } catch (Exception $e) {
        $age = 'Invalid date';
    }
}



// Аватар по умолчанию
$avatar = !empty($user['avatar']) ? htmlspecialchars($user['avatar']) : 'images/default_avatar.png';
$avatarPath = !empty($user['avatar']) ? $user['avatar'] : 'images/default_avatar.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
  <link href='https://fonts.googleapis.com/css?family=Anton' rel='stylesheet'>
  
  <style>
    .profile-header {
        background: linear-gradient(135deg, #4158D0 0%, #C850C0 100%);
        height: 150px;
        border-radius: 15px;
    }
    .profile-pic {
      width: 120px;
      height: 120px;
      object-fit: cover;       /* обрезает лишнее, не растягивает */
      object-position: center; /* центрирует содержимое */
      border-radius: 50%;
      border: 4px solid #fff;
      background-color: #fff;
    }

    .settings-card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }
    .settings-card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    .activity-item {
        border-left: 2px solid #e9ecef;
        padding-left: 20px;
        position: relative;
    }
    .activity-item::before {
        content: '';
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #4158D0;
        position: absolute;
        left: -7px;
        top: 5px;
    }
    .profile-header {
  transition: background 0.4s ease;
}

  </style>
</head>
<body>
  
<?php include 'includes/header.php'; ?>
<?php if (isset($_SESSION['success'])): ?>
  <div id="success-alert" class="position-fixed top-0 start-50 translate-middle-x alert alert-success text-center shadow" 
       style="z-index: 1050; margin-top: 60px; min-width: 300px;">
    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
  </div>
<?php endif; ?>



<div class="bg-light">
  <div class="container py-5">
    <div class="row">
      <div class="col-12 mb-4">

        <!-- Профильный блок -->
        <div class="profile-header position-relative mb-4 rounded" id="profileHeader" style="background: linear-gradient(to right, #4f46e5, #ec4899); height: 150px;">
          <div class="position-absolute top-0 end-0 p-3">
            <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#coverModal">
              <i class="fas fa-edit me-2"></i>Edit Cover
            </button>
          </div>
        </div>



        <div class="text-center">
          <div class="position-relative d-inline-block">


          <img src="<?= $avatar ?>" class="rounded-circle profile-pic" alt="Avatar" data-bs-toggle="modal" data-bs-target="#avatarModal" style="cursor: pointer;">



            <form method="POST" action="upload_avatar.php" enctype="multipart/form-data" class="position-absolute bottom-0 end-0">
              <label class="btn btn-primary btn-sm rounded-circle m-0">
                <i class="fas fa-camera"></i>
                <input type="file" name="avatar" onchange="this.form.submit()" hidden>
              </label>
            </form>
          </div>
          <h3 class="mt-3 mb-1"><?= htmlspecialchars($user['username']) ?></h3>
          <p class="text-muted mb-3"><?= htmlspecialchars($user['email']) ?></p>
        </div>
      </div>

      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body p-0">
            <div class="row g-0">
              <div class="col-lg-3 border-end">
                <div class="p-4">
                  <div class="list-group">
                    <a href="todo.php" class="list-group-item list-group-item-action d-flex align-items-center <?= basename($_SERVER['PHP_SELF']) === 'todo.php' ? 'active' : '' ?>">
                      <i class="fas fa-list-check me-2"></i> To-Do List
                    </a>
                    <a href="training_diary.php" class="list-group-item list-group-item-action d-flex align-items-center <?= basename($_SERVER['PHP_SELF']) === 'training_diary.php' ? 'active' : '' ?>">
                      <i class="fas fa-book-open me-2"></i> Training Diary
                    </a>
                    <a href="progress.php" class="list-group-item list-group-item-action d-flex align-items-center <?= basename($_SERVER['PHP_SELF']) === 'progress.php' ? 'active' : '' ?>">
                      <i class="fas fa-chart-line me-2"></i> Progress
                    </a>
                    <a href="index.php" class="list-group-item list-group-item-action d-flex align-items-center <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">
                      <i class="fas fa-home me-2"></i> Home
                    </a>
                    <?php if (in_array($_SESSION['role'], ['admin', 'superadmin'])): ?>
                    <a href="admin_panel.php" class="list-group-item list-group-item-action d-flex align-items-center <?= basename($_SERVER['PHP_SELF']) === 'admin_panel.php' ? 'active' : '' ?>">
                      <i class="fas fa-user-shield me-2"></i> Admin Panel
                    </a>
                    <?php endif; ?>
                    <a href="logout.php" class="list-group-item list-group-item-action d-flex align-items-center text-danger">
                      <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a>
                  </div>

                </div>
              </div>
              <div class="col-lg-9">
                <div class="p-4">
                  <h5 class="mb-4">Personal Info</h5>
                  <div class="row g-3">
                    <div class="col-md-6"><strong>Registration Date:</strong> <?= htmlspecialchars($user['registrationDate']) ?></div>
                    <div class="col-md-6"><strong>Last Login:</strong> <?= htmlspecialchars($user['lastLoginDate']) ?></div>
                    <div class="col-md-6"><strong>Premium:</strong> <?= !empty($user['isPremium']) ? 'Yes' : 'No' ?></div>
                    <div class="col-md-6"><strong>Gender:</strong> <?= htmlspecialchars($user['gender']) ?></div>
                    <div class="col-md-6"><strong>Birthdate:</strong> <?= $birthdate ?></div>
                    <div class="col-md-6"><strong>Age:</strong> <?= $age ?></div>
                    <div class="col-md-6"><strong>Height:</strong> <?= htmlspecialchars($user['height']) ?> cm</div>
                    <div class="col-md-6"><strong>Weight:</strong> <?= htmlspecialchars($user['weight']) ?> kg</div>
                    <p><strong>Premium:</strong> <?= isset($user['isPremium']) && $user['isPremium'] ? 'Yes' : 'No' ?></p>

                  </div>

                  <hr class="my-4">
                  <h5 class="mb-3">Actions</h5>
                  <a href="todo.php" class="btn btn-outline-primary me-2 mb-2"><i class="fas fa-tasks me-1"></i>To-Do List</a>
                  <a href="training_diary.php" class="btn btn-outline-secondary me-2 mb-2"><i class="fas fa-sticky-note me-1"></i>Training diary</a>
                  <a href="progress.php" class="btn btn-outline-success me-2 mb-2"><i class="fas fa-chart-bar me-1"></i>Track Progress</a>
                  <a href="index.php" class="btn btn-outline-dark me-2 mb-2"><i class="fas fa-home me-1"></i>Home</a>

                  <?php if (in_array($_SESSION['role'], ['admin', 'superadmin'])): ?>
                    <a href="admin_panel.php" class="btn btn-outline-dark me-2 mb-2">
                      <i class="fas fa-user-shield me-1"></i> Admin Panel
                    </a>
                  <?php endif; ?>
                  <a href="logout.php" class="btn btn-danger me-2 mb-2"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>

                  <div class="row g-4 mt-5">
                    <!-- Become a Trainer -->
                    <div class="col-md-6">
                      <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                          <i class="fas fa-dumbbell fa-2x text-primary mb-3"></i>
                          <h5 class="card-title">Become a Trainer</h5>
                          <p class="card-text">Submit your profile and become visible to clients.</p>
                          <a href="<?= $isPremium ? 'trainer_submit.php' : '#' ?>" 
                            class="btn btn-primary <?= $isPremium ? '' : 'premium-lock' ?>" 
                            data-target="#premiumModal">Apply</a>
                        </div>
                      </div>
                    </div>

                    <!-- Find a Trainer -->
                    <div class="col-md-6">
                      <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                          <i class="fas fa-search fa-2x text-success mb-3"></i>
                          <h5 class="card-title">Find a Trainer</h5>
                          <p class="card-text">Browse certified personal trainers for your fitness goals.</p>
                          <a href="<?= $isPremium ? 'trainers_page.php' : '#' ?>" 
                            class="btn btn-success <?= $isPremium ? '' : 'premium-lock' ?>" 
                            data-target="#premiumModal">Explore</a>
                        </div>
                      </div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>  
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="coverModal" tabindex="-1" aria-labelledby="coverModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="coverModalLabel">Change Cover Color</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <label for="coverColorPicker" class="form-label">Choose color</label>
        <input type="color" id="coverColorPicker" class="form-control form-control-color mx-auto" value="#4f46e5" title="Choose your color">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="applyCoverColor">Apply</button>
      </div>
    </div>
  </div>
</div>


<!-- Модальное окно для увеличенного аватара -->
<div class="modal fade" id="avatarModal" tabindex="-1" aria-labelledby="avatarModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-light">
      <div class="modal-header">
        <h5 class="modal-title" id="avatarModalLabel">Your Avatar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img src="<?= $avatar ?>" class="img-fluid rounded" alt="Enlarged Avatar">
      </div>
    </div>
  </div>
</div>

<!-- Premium Only Modal -->
<div class="modal fade" id="premiumModal" tabindex="-1" aria-labelledby="premiumModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="premiumModalLabel">Premium Feature</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <i class="bi bi-lock-fill display-4 text-warning mb-3"></i>
        <p>This feature is only available for Premium users.</p>
        <a href="#" class="btn btn-warning">Upgrade to Premium</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
  setTimeout(() => {
    const alert = document.getElementById('success-alert');
    if (alert) {
      alert.style.transition = 'opacity 0.5s ease';
      alert.style.opacity = '0';
      setTimeout(() => alert.remove(), 500); // Удаляем элемент из DOM
    }
  }, 3000); // 3 секунды
</script>

<script>
  document.querySelectorAll('.premium-lock').forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      const modal = new bootstrap.Modal(document.getElementById('premiumModal'));
      modal.show();
    });
  });
</script>

<script>
  document.getElementById('applyCoverColor').addEventListener('click', function () {
    const color = document.getElementById('coverColorPicker').value;
    const header = document.getElementById('profileHeader');

    header.style.background = color;
    const modal = bootstrap.Modal.getInstance(document.getElementById('coverModal'));
    modal.hide();
  });
</script>


</body>
</html>
