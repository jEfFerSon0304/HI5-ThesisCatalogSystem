<?php
include '../PHP/db_connect.php';
session_start();
date_default_timezone_set('Asia/Manila');

$error = "";

// 🧠 Handle Login Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // 🕓 Get library hours (if any)
    $open_time = null;
    $close_time = null;
    $checkTable = $conn->query("SHOW TABLES LIKE 'tbl_system_settings'");
    if ($checkTable && $checkTable->num_rows > 0) {
        $res = $conn->query("SELECT setting_key, setting_value FROM tbl_system_settings WHERE setting_key IN ('library_open_time', 'library_close_time')");
        while ($row = $res->fetch_assoc()) {
            if ($row['setting_key'] === 'library_open_time') $open_time = $row['setting_value'];
            if ($row['setting_key'] === 'library_close_time') $close_time = $row['setting_value'];
        }
    }

    if ($role === 'admin') {
        // ✅ Admin login (no time restriction)
        $hashed = md5($password);
        $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $username, $hashed);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            $_SESSION['admin'] = $admin['username'];
            $_SESSION['role'] = 'admin';
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "❌ Invalid Super Admin username or password.";
        }
    } elseif ($role === 'librarian') {
        // 🧠 Librarian login (with time restriction)
        $email = $username;
        $stmt = $conn->prepare("SELECT * FROM tbl_librarians WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // 🕓 Time restriction check
            if ($open_time && $close_time) {
                $current_time = date('H:i');
                if ($current_time < $open_time || $current_time > $close_time) {
                    $error = "⏰ The library system is closed. Please log in between $open_time and $close_time.";
                }
            }

            // Only proceed if within allowed time
            if (empty($error)) {
                if ($user['status'] === 'pending') {
                    $error = "🕓 Your account is still pending approval by the admin.";
                } elseif ($user['status'] === 'inactive') {
                    $error = "🚫 Your account has been deactivated. Please contact the admin.";
                } elseif (password_verify($password, $user['password'])) {
                    $_SESSION['librarian_id'] = $user['librarian_id'];
                    $_SESSION['fullname'] = $user['fullname'];
                    $_SESSION['section'] = $user['section'];
                    $_SESSION['role'] = 'librarian';

                    $update = $conn->prepare("UPDATE tbl_librarians SET last_login = NOW() WHERE librarian_id = ?");
                    $update->bind_param("i", $user['librarian_id']);
                    $update->execute();

                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = "❌ Incorrect password.";
                }
            }
        } else {
            $error = "⚠️ Librarian account not found.";
        }
    } else {
        $error = "⚠️ Invalid role selected.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login | CEIT Thesis Hub</title>
    <link rel="icon" type="image/png" href="pictures/Logo.png" />
    <link rel="stylesheet" href="style.css" />

    <style>

    </style>
</head>

<body>
    <main class="login-main">
        <div class="login-container">
            <div class="login-box">
                <img src="pictures/Logo.png" alt="CEIT Thesis Hub Logo" class="logo" />
                <h2>CEIT Thesis Hub Login</h2>

                <form method="POST" action="">
                    <div class="input-group">
                        <input type="text" name="username" placeholder="Username or Email" required />
                    </div>

                    <div class="input-group password-group">
                        <input
                            type="password"
                            name="password"
                            placeholder="Password"
                            required
                            autocomplete="off"
                            oncopy="return false"
                            onpaste="return false">
                        <span class="toggle-password">
                            <img src="pictures/close-eye.png" alt="Toggle Password">
                        </span>
                    </div>

                    <div class="input-group select-group" style="margin-bottom: 10px;">
                        <select name="role" required>
                            <option value="" disabled selected>Select Role</option>
                            <option value="admin">Super Admin</option>
                            <option value="librarian">Librarian</option>
                        </select>
                    </div>


                    <button type="submit" class="login-btn">LOG IN</button>
                    <a href="forgot-password.php" class="forgot-link">Forgot password?</a>

                    <?php if (!empty($error)): ?>
                        <p style="color:red; margin-top:10px; text-align:center;"><?php echo $error; ?></p>
                    <?php endif; ?>
                </form>

                <p style="margin-top:15px;">
                    Not yet a librarian? <a href="register-librarian.php">Register here</a>.
                </p>
            </div>
        </div>
    </main>

    <script>
        // ✅ Password visibility toggle (fixed)
        const passwordInput = document.querySelector('input[name="password"]');
        const toggleSpan = document.querySelector('.toggle-password');
        const toggleImg = toggleSpan.querySelector('img');

        toggleSpan.addEventListener("click", () => {
            const isHidden = passwordInput.getAttribute("type") === "password";
            passwordInput.setAttribute("type", isHidden ? "text" : "password");
            toggleImg.src = isHidden ?
                "pictures/visible.png" // 👁️ when visible
                :
                "pictures/close-eye.png"; // 🙈 when hidden
        });
    </script>


</body>

</html>