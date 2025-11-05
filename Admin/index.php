<?php
include '../PHP/db_connect.php';
session_start();
date_default_timezone_set('Asia/Manila');

$error = "";

// 🧹 Clear any previous session (optional safety)
if (isset($_SESSION['admin']) || isset($_SESSION['librarian_id'])) {
    session_destroy();
    session_start();
}

// 🧠 Handle Login Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role']; // 'admin' or 'librarian'

    // ✅ SUPER ADMIN LOGIN
    if ($role === 'admin') {
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
    }

    // ✅ LIBRARIAN LOGIN
    if ($role === 'librarian') {
        $email = $username; // field reused as email for librarians
        $stmt = $conn->prepare("SELECT * FROM tbl_librarians WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // 🔍 Account status check
            if ($user['status'] === 'pending') {
                $error = "🕓 Your account is still pending approval by the admin.";
            } elseif ($user['status'] === 'inactive') {
                $error = "🚫 Your account has been deactivated. Please contact the admin.";
            } else {
                // 🕒 Librarian access window (8 AM - 5 PM)
                // $hour = (int) date('H');
                // if ($hour < 8 || $hour >= 17) {
                //     $error = "⏰ Library system access is only available between 8:00 AM and 5:00 PM.";
                // } else {
                // 🔐 Verify password
                if (password_verify($password, $user['password'])) {
                    $_SESSION['librarian_id'] = $user['librarian_id'];
                    $_SESSION['fullname'] = $user['fullname'];
                    $_SESSION['section'] = $user['section'];
                    $_SESSION['role'] = 'librarian';

                    // 🕓 Update last login time
                    $update = $conn->prepare("UPDATE tbl_librarians SET last_login = NOW() WHERE librarian_id = ?");
                    $update->bind_param("i", $user['librarian_id']);
                    $update->execute();

                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = "❌ Incorrect password.";
                }
            }
        }
    } else {
        $error = "⚠️ Librarian account not found.";
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
</head>

<body>
    <main class="login-main">
        <div class="login-container">
            <div class="login-box">
                <img src="pictures/Logo.png" alt="CEIT Thesis Hub Logo" class="logo" />
                <h2>CEIT Thesis Hub Login</h2>

                <form method="POST" action="">
                    <!-- Username / Email -->
                    <div class="input-group">
                        <input type="text" name="username" placeholder="Username or Email" required />
                        <img src="pictures/user.png" class="icon" />
                    </div>

                    <!-- Password -->
                    <div class="input-group">
                        <input type="password" name="password" placeholder="Password" required />
                        <img src="pictures/lock.png" class="icon" />
                    </div>

                    <!-- Role -->
                    <div class="input-group" style="margin-bottom: 10px;">
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
</body>

</html>