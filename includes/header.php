<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$avatarPath = 'images/default_avatar.png'; // дефолтный путь

if (isset($_SESSION['user_id'])) {
    $conn = new mysqli("localhost", "root", "", "gymbridges");
    if (!$conn->connect_error) {
        $user_id = $_SESSION['user_id'];
        $result = $conn->query("SELECT avatar FROM users WHERE userID = $user_id");
        if ($result && $user = $result->fetch_assoc()) {
            if (!empty($user['avatar'])) {
                $avatarPath = $user['avatar'];
            }
        }
        $conn->close();
    }
}
?>


<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="images/logo.png" alt="Logo" style="height: 40px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                      <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#target-muscle-map"  onclick="scrollToSection()">Muscle selection</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="programs.html">Training programs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="aboutus.html">About us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#section2">Contacts & Map</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                      <!-- If user is logged in -->
                        <li class="nav-item d-flex align-items-center">
                            <a class="nav-link d-flex align-items-center" href="welcome.php">
                            <img src="<?= htmlspecialchars($avatarPath) ?>" alt="Avatar" class="rounded-circle me-2" style="height: 30px; width: 30px; object-fit: cover;">
                            Your profile
                            </a>
                        </li>
                      <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                      </li>
                    <?php else: ?>
                      <!-- If user is not logged in -->
                      <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" href="register.php">Register</a>
                      </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>