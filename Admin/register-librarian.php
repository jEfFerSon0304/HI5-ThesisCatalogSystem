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

    // 🧩 Password match check
    if ($password !== $confirm) {
        $message = "⚠️ Passwords do not match. Please try again.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // 1️⃣ Check if an active librarian already exists in this section
        $check = $conn->prepare("SELECT COUNT(*) AS c FROM tbl_librarians WHERE section=? AND status='active'");
        $check->bind_param("s", $section);
        $check->execute();
        $count = $check->get_result()->fetch_assoc()['c'];

        // 2️⃣ Determine status
        $status = ($count == 0) ? 'active' : 'pending';

        // 3️⃣ Insert librarian record
        $insert = $conn->prepare("
            INSERT INTO tbl_librarians (fullname, email, password, section, status)
            VALUES (?, ?, ?, ?, ?)
        ");
        $fullname = $firstname . ' ' . $lastname;
        $insert->bind_param("sssss", $fullname, $email, $hashedPassword, $section, $status);

        if ($insert->execute()) {
            if ($status === 'active') {
                $message = "✅ Registration successful! You are now an active librarian.";
            } else {
                $message = "🕓 Registration submitted. Awaiting admin approval.";
            }
        } else {
            $message = "⚠️ Something went wrong. Please try again.";
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
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <main class="login-main">
        <div class="login-container">
            <div class="login-box">
                <img src="pictures/Logo.png" alt="CEIT Logo" class="logo">
                <h2>Librarian Registration</h2>
                <form method="POST" action="">
                    <div class="input-group">
                        <input type="text" name="firstname" placeholder="First Name" required>
                    </div>
                    <div class="input-group">
                        <input type="text" name="lastname" placeholder="Last Name" required>
                    </div>
                    <div class="input-group">
                        <input type="email" name="email" placeholder="Email Address" required>
                    </div>
                    <div class="input-group">
                        <input type="text" name="section" placeholder="Section (e.g., BSIT-3A)" required>
                    </div>
                    <div class="input-group">
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="input-group">
                        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    </div>
                    <button type="submit" class="login-btn">Register</button>
                </form>
                <?php if (!empty($message)) echo "<p style='margin-top:10px;'>$message</p>"; ?>
                <p style="margin-top:15px;"><a href="index.php">Back to Login</a></p>
            </div>
        </div>
    </main>
</body>

</html>