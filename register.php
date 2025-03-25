<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'gymbridges');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
$message_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $weight = trim($_POST['weight']);
    $gender = trim($_POST['gender']);
    $age = trim($_POST['age']);
    $height = trim($_POST['height']);

    // Валидация
    $errors = [];

    // Проверка пустых строк
    if (empty($username)) $errors[] = "Username is required.";
    if (empty($email)) $errors[] = "Email is required.";
    if (empty($password)) $errors[] = "Password is required.";
    if (empty($confirm_password)) $errors[] = "Please confirm your password.";
    if (empty($weight)) $errors[] = "Weight is required.";
    if (empty($gender)) $errors[] = "Gender is required.";
    if (empty($age)) $errors[] = "Age is required.";
    if (empty($height)) $errors[] = "Height is required.";

    // Проверка на совпадение паролей
    if ($password !== $confirm_password) $errors[] = "Passwords do not match.";

    // Проверка формата email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";

    // Валидация чисел и диапазонов
    if (!is_numeric($weight) || $weight < 5 || $weight > 250) $errors[] = "Weight must be a number between 5 and 250 kg.";
    if (!is_numeric($age) || $age < 0 || $age > 120) $errors[] = "Age must be a number between 0 and 120.";
    if (!is_numeric($height) || $height < 30 || $height > 250) $errors[] = "Height must be a number between 30 and 250 cm.";

    // Проверка уникальности пользователя по email и никнейму
    $user_check_query = "SELECT * FROM users WHERE email = '$email' OR username = '$username' LIMIT 1";
    $result = $conn->query($user_check_query);
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($user['email'] === $email) {
            $errors[] = "Email is already registered.";
        }
        if ($user['username'] === $username) {
            $errors[] = "Username is already taken.";
        }
    }

    // Проверка на наличие ошибок
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $registrationDate = date('Y-m-d');

        $sql = "INSERT INTO users (username, email, password, weight, gender, age, height, registrationDate)
                VALUES ('$username', '$email', '$password_hash', '$weight', '$gender', '$age', '$height', '$registrationDate')";

        if ($conn->query($sql) === TRUE) {
            $message = "Registration successful";
            $message_type = "success";
            header("Location: login.php");
            exit();
        } else {
            $message = "Error: " . $conn->error;
            $message_type = "danger";
        }
    } else {
        $message = implode("<br>", $errors);
        $message_type = "danger";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registration</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="custom.css" rel="stylesheet">
    <script>
        function validateForm() {
            var username = document.getElementById("username").value.trim();
            var email = document.getElementById("email").value.trim();
            var password = document.getElementById("password").value.trim();
            var confirm_password = document.getElementById("confirm_password").value.trim();
            var weight = document.getElementById("weight").value.trim();
            var gender = document.getElementById("gender").value.trim();
            var age = document.getElementById("age").value.trim();
            var height = document.getElementById("height").value.trim();

            if (username === "" || email === "" || password === "" || confirm_password === "" || weight === "" || gender === "" || age === "" || height === "") {
                alert("All fields are required.");
                return false;
            }

            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                alert("Invalid email format.");
                return false;
            }

            if (password !== confirm_password) {
                alert("Passwords do not match.");
                return false;
            }

            if (isNaN(weight) || weight < 5 || weight > 250) {
                alert("Weight must be a number between 5 and 250 kg.");
                return false;
            }

            if (isNaN(age) || age < 0 || age > 120) {
                alert("Age must be a number between 0 and 120.");
                return false;
            }

            if (isNaN(height) || height < 30 || height > 250) {
                alert("Height must be a number between 30 and 250 cm.");
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h2>Register</h2>
                </div>
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $message_type; ?>" role="alert">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="register.php" onsubmit="return validateForm()">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <div class="form-group">
                            <label for="weight">Weight (kg)</label>
                            <input type="number" step="0.1" class="form-control" id="weight" name="weight" required>
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select class="form-control" id="gender" name="gender" required>
                                <option value="">Select</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="age">Age</label>
                            <input type="number" class="form-control" id="age" name="age" required>
                        </div>
                        <div class="form-group">
                            <label for="height">Height (cm)</label>
                            <input type="number" step="0.1" class="form-control" id="height" name="height" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Register</button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
                    <p>To main <a href="index.html">LETS'GO</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
