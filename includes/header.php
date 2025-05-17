<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$avatarPath = $avatarPath ?? 'images/default_avatar.png'; // используем уже заданный путь, если есть

$currentPage = basename($_SERVER['PHP_SELF']);
$scrollLink = $currentPage === 'index.php' ? '#muscle-map' : 'index.php#muscle-map';
?>

<header>
<style>

/* Стили для выпадающего меню */
.navbar-nav .dropdown-menu {
  border-radius: 8px;
  border: 1px solid rgba(0, 0, 0, 0.05);
  box-shadow: 0 8px 16px rgba(0,0,0,0.12);
  padding: 0.5rem 0;
  min-width: 200px;
  transition: all 0.2s ease-in-out;
}

/* Элементы меню */
.dropdown-item {
  font-size: 0.95rem;
  font-weight: 400;
  padding: 8px 20px;
  color: #333;
  transition: background-color 0.2s ease, color 0.2s ease;
}

.dropdown-item:hover {
  background-color: #f1f1f1;
  color: #0d6efd;
}

/* Кнопка Home — нормальный белый текст */
.navbar .nav-link.dropdown-toggle {
  color: #fff;
}

.navbar .nav-link.dropdown-toggle:hover {
  color: #dee2e6;
}

/* Отображение подменю при наведении */
.navbar-nav .dropdown:hover .dropdown-menu {
  display: block;
  margin-top: 0;
}

/* Чистый, читаемый стиль для dropdown */
.navbar .dropdown-menu .dropdown-item {
  font-family: 'Inter', sans-serif;
  font-weight: 400;
  font-size: 0.95rem;
  color: #333;
  padding: 8px 16px;
  transition: background-color 0.2s ease, color 0.2s ease;
  background-color: white;
}

.navbar .dropdown-menu .dropdown-item:hover {
  background-color: #f0f0f0;
  color: #0d6efd;
}



</style>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand me-4" href="index.php">
      <img src="images/logo.png" alt="Logo" style="height: 40px;">
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <!-- ЛЕВОЕ МЕНЮ -->
      <ul class="navbar-nav me-auto ps-3"> <!-- отступ слева (от логотипа) -->


        <li class="nav-item dropdown position-relative">
          <a class="nav-link dropdown-toggle" href="index.php" id="homeDropdown" role="button">
            Home
          </a>
          <ul class="dropdown-menu" aria-labelledby="homeDropdown">
            <li><a class="dropdown-item" href="index.php#muscle-map-section">Muscle selection</a></li>
            <li><a class="dropdown-item" href="index.php#map-section">Contacts & Map</a></li>
          </ul>
        </li>






        <li class="nav-item"><a class="nav-link" href="exercises_page.php">Exercises</a></li>
        <li class="nav-item"><a class="nav-link" href="workout_programs_page.php">Training programs</a></li>
        <li class="nav-item"><a class="nav-link" href="calorie_calculator.php">Calorie calculator</a></li>
        <li class="nav-item"><a class="nav-link" href="aboutus.html">About us</a></li>

      </ul>

      <!-- ПРАВОЕ МЕНЮ -->
      <ul class="navbar-nav d-flex align-items-center">
        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item me-3">
            <a class="nav-link d-flex align-items-center gap-2" href="welcome.php">
              <span>Your profile</span>
              <img src="<?= htmlspecialchars($avatarPath) ?>" class="rounded-circle" alt="Avatar" style="width: 32px; height: 32px; object-fit: cover;">
            </a>
          </li>
          <li class="nav-item me-3">
            <a class="nav-link" href="logout.php">Logout</a>
          </li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>



</header>