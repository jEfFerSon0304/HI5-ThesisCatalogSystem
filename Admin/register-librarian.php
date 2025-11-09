<?php
include "../PHP/db_connect.php";
date_default_timezone_set('Asia/Manila');

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname']);
    $lastname  = trim($_POST['lastname']);
    $email     = trim($_POST['email']);
    $section   = trim($_POST['section']);
    $password  = $_POST['password'];
    $confirm   = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $message = "⚠️ Passwords do not match. Please try again.";
    } else {
        $checkEmail = $conn->prepare("SELECT COUNT(*) AS c FROM tbl_librarians WHERE email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $emailExists = $checkEmail->get_result()->fetch_assoc()['c'] > 0;

        if ($emailExists) {
            $message = "⚠️ Email already exists. Please use a different one.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $check = $conn->prepare("SELECT COUNT(*) AS c FROM tbl_librarians WHERE section=? AND status='active'");
            $check->bind_param("s", $section);
            $check->execute();
            $count = $check->get_result()->fetch_assoc()['c'];
            $status = ($count == 0) ? 'active' : 'pending';
            $insert = $conn->prepare("
                INSERT INTO tbl_librarians (fullname, email, password, section, status)
                VALUES (?, ?, ?, ?, ?)
            ");
            $fullname = $firstname . ' ' . $lastname;
            $insert->bind_param("sssss", $fullname, $email, $hashedPassword, $section, $status);

            if ($insert->execute()) {
                if ($status === 'active') {
                    header("Location: index.php");
                    exit();
                } else {
                    header("Location: ../home.html");
                    exit();
                }
            } else {
                $message = "⚠️ Something went wrong. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Registration</title>
    <link rel="icon" type="image/png" href="pictures/Logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <button id="themeToggle">🌙</button>

    <div class="register-container">
        <img src="pictures/Logo.png" alt="CEIT Logo">
        <h2>Librarian Registration</h2>

        <form method="POST" action="">
            <div class="name-group">
                <input type="text" name="firstname" placeholder="First Name" required>
                <input type="text" name="lastname" placeholder="Last Name" required>
            </div>

            <input type="text" name="section" placeholder="Section" required>
            <input type="email" name="email" placeholder="Email Address" required>

            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>

            <div class="password-rules">
                <strong>Password must:</strong><br>
                • Be at least 8 characters long<br>
                • Contain one uppercase letter<br>
                • Contain one lowercase letter<br>
                • Contain one number or symbol
            </div>

            <button type="submit" class="login-btn">Register</button>
        </form>

        <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>

        <div class="footer-link">
            <a href="index.php">← Back to Login</a>
        </div>
    </div>

    <script>
        // Password visibility toggle
        const passwordInput = document.querySelector('input[name="password"]');
        const passwordIcon = document.querySelector('.input-group img.icon');

        passwordIcon.addEventListener("click", () => {
            const isHidden = passwordInput.getAttribute("type") === "password";
            passwordInput.setAttribute("type", isHidden ? "text" : "password");
            passwordIcon.src = isHidden ? "pictures/eye.png" : "pictures/lock.png"; // make sure you have an eye icon
        });
    </script>
</body>

</html>