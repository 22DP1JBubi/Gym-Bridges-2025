<?php session_start();

$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

$today = date('Y-m-d');
$minBirthdate = date('Y-m-d', strtotime('-120 years'));
$maxBirthdate = date('Y-m-d', strtotime('-1 year')); // минимум 1 год

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      padding-top: 50px;
      padding-bottom: 50px;
      background: url("images/background.jpg") no-repeat center center fixed;
      background-size: cover;
    }
    .register-container {
      background-color: white;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      max-width: 600px;
      margin: auto;
    }
    .form-text.text-danger {
      display: none;
    }
    .main-link {
            text-align: center;
            margin-top: 10px;
            font-size: 14px;
    
        }
  </style>
</head>
<body>

<div class="register-container">
  <h2 class="text-center mb-4">Create an Account</h2>

  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
  <?php endif; ?>
  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
  <?php endif; ?>

    

  <form method="POST" action="register_process.php" onsubmit="return validateForm()">
    <div class="form-group">
      <label>Username</label>
      <input type="text" class="form-control" id="username" name="username"  value="<?= htmlspecialchars($form_data['username'] ?? '') ?>" placeholder="Enter username">
      <small id="username-error" class="form-text text-danger">Invalid username</small>
    </div>

    <div class="form-group">
      <label>Email</label>
      <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($form_data['email'] ?? '') ?>" placeholder="Enter email">
      <small id="email-error" class="form-text text-danger">Invalid email</small>
    </div>

    <div class="form-group">
      <label>Password</label>
      <div class="input-group">
        <input type="password" class="form-control" id="password" name="password" placeholder="At least 8 characters, 1 uppercase, 1 number, 1 symbol">
        <div class="input-group-append">
          <button class="btn btn-outline-secondary" type="button" id="toggle-password">
            <i class="bi bi-eye" id="password-icon"></i>
          </button>
        </div>
      </div>
      <div class="mt-2 d-flex gap-2">
        <button type="button" class="btn btn-sm btn-light" id="generate-password">Generate password</button>
        <button type="button" class="btn btn-sm btn-secondary" id="copy-password">Copy password</button>
      </div>
      <small id="password-error" class="form-text text-danger">Password must contain uppercase, number and symbol</small>
    </div>

    <div class="form-group">
      <label>Confirm Password</label>
      <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm your password">
      <small id="confirm-password-error" class="form-text text-danger">Passwords do not match</small>
    </div>

    <div class="form-row">
      <div class="form-group col-md-6">
        <label>Weight (kg)</label>
        <input type="number" step="0.1" min="5" max="250" class="form-control" id="weight" name="weight" value="<?= htmlspecialchars($form_data['weight'] ?? '') ?>" placeholder="Enter weight">
        <small id="weight-error" class="form-text text-danger">Enter weight between 5–250</small>
      </div>

      <div class="form-group col-md-6">
        <label>Height (cm)</label>
        <input type="number" step="0.1" min="30" max="250" class="form-control" id="height" name="height" value="<?= htmlspecialchars($form_data['height'] ?? '') ?>" placeholder="Enter height">
        <small id="height-error" class="form-text text-danger">Height must be 30–250</small>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group col-md-6">
        <label for="birthdate">Date of Birth</label>
        <input type="date" name="birthdate"
          class="form-control"
          min="<?= $minBirthdate ?>"
          max="<?= $maxBirthdate ?>"
          value="<?= htmlspecialchars($form_data['birthdate'] ?? '') ?>"
          required>

      </div>


      <div class="form-group col-md-6">
        <label>Gender</label>
        <select class="form-control" id="gender" name="gender">
          <option value="">Select</option>
          <option value="Male" <?= (isset($form_data['gender']) && $form_data['gender'] === 'Male') ? 'selected' : '' ?>>Male</option>
          <option value="Female" <?= (isset($form_data['gender']) && $form_data['gender'] === 'Female') ? 'selected' : '' ?>>Female</option>
          <option value="Other" <?= (isset($form_data['gender']) && $form_data['gender'] === 'Other') ? 'selected' : '' ?>>Other</option>
        </select>
        <small id="gender-error" class="form-text text-danger">Please select gender</small>
      </div>
    </div>

    <button type="submit" class="btn btn-primary btn-block">Register</button>
  </form>

  <p class="text-center mt-3">Already have an account? <a href="login.php">Login here</a></p>
  <div class="main-link">
      <a href="index.php">Go to main page</a>
    </div>
</div>

<script>
  document.getElementById('toggle-password').addEventListener('click', function () {
    const pw = document.getElementById('password');
    const icon = document.getElementById('password-icon');
    pw.type = pw.type === 'password' ? 'text' : 'password';
    icon.classList.toggle('bi-eye');
    icon.classList.toggle('bi-eye-slash');
  });

  document.getElementById('generate-password').addEventListener('click', function () {
    fetch('password_generator.php', { method: 'POST' })
      .then(res => res.text())
      .then(password => {
        document.getElementById('password').value = password;
        document.getElementById('confirm_password').value = '';
      });
  });

  document.getElementById('copy-password').addEventListener('click', function () {
    const passwordField = document.getElementById('password');
    passwordField.select();
    passwordField.setSelectionRange(0, 99999); // for mobile
    navigator.clipboard.writeText(passwordField.value)
      .then(() => console.log('Password copied'))
      .catch(err => console.error('Copy failed', err));
  });

  function validateForm() {
    let valid = true;
    const show = id => { document.getElementById(id).style.display = 'block'; valid = false; };
    const hideAll = () => document.querySelectorAll('.form-text.text-danger').forEach(e => e.style.display = 'none');
    hideAll();

    const username = document.getElementById('username').value.trim();
    const email = document.getElementById('email').value.trim();
    const pw = document.getElementById('password').value;
    const cpw = document.getElementById('confirm_password').value;
    const weight = parseFloat(document.getElementById('weight').value);
    const gender = document.getElementById('gender').value;
    const age = parseInt(document.getElementById('age').value);
    const height = parseFloat(document.getElementById('height').value);

    if (!/^[a-zA-Z0-9_]{3,}$/.test(username)) show('username-error');
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) show('email-error');
    if (pw.length < 8 || !/[A-Z]/.test(pw) || !/\d/.test(pw) || !/[\W_]/.test(pw)) show('password-error');
    if (pw !== cpw) show('confirm-password-error');
    if (isNaN(weight) || weight < 5 || weight > 250) show('weight-error');
    if (!gender) show('gender-error');
    if (isNaN(age) || age < 1 || age > 120) show('age-error');
    if (isNaN(height) || height < 30 || height > 250) show('height-error');

    return valid;
  }
</script>

</body>
</html>
