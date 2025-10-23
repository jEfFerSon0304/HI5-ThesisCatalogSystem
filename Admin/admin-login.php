<?php
include '../PHP/db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM admin WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION['admin'] = $username;
        header("Location: index.html"); // Redirect after login
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Log In</title>
  <link rel="icon" type="image/png" href="pictures/Logo.png">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <main class="login-main">
    <div class="login-container">
      <div class="login-box">
        <img src="pictures/Logo.png" alt="CEIT Thesis Hub Logo" class="logo">
        <h2>Admin Log In</h2>

        <form method="POST" action="">
          <div class="input-group">
            <input type="text" name="username" placeholder="USERNAME" required>
            <img src="pictures/user.png" class="icon">
          </div>
          <div class="input-group">
            <input type="password" name="password" placeholder="PASSWORD" required>
            <img src="pictures/lock.png" class="icon">
          </div>

          <button type="submit" class="login-btn">LOG IN</button>
          <a href="#" class="forgot-link">Forgot password?</a>

          <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
        </form>
      </div>
    </div>
  </main>
</body>
</html>
