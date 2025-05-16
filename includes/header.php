<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$avatarPath = $avatarPath ?? 'images/default_avatar.png'; // используем уже заданный путь, если есть
?>

<header>
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
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>

        <?php
          $currentPage = basename($_SERVER['PHP_SELF']);
          $scrollLink = $currentPage === 'index.php' ? '#muscle-map' : 'index.php#muscle-map';
        ?>
        <li class="nav-item">
          <a class="nav-link" href="index.php#muscle-map-section">Muscle selection</a>
        </li>



        <li class="nav-item"><a class="nav-link" href="exercises_page.php">Exercises</a></li>
        <li class="nav-item"><a class="nav-link" href="workout_programs_page.php">Training programs</a></li>
        <li class="nav-item"><a class="nav-link" href="aboutus.html">About us</a></li>
        <li class="nav-item">
          <a class="nav-link" href="index.php#map-section">Contacts & Map</a>
        </li>

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