<?php
include "../PHP/db_connect.php";
session_start();
date_default_timezone_set('Asia/Manila');

// 🔒 Ensure logged in
if (!isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}

$role = $_SESSION['role'];
$message = "";

// 🧩 Get current user info
if ($role === 'admin') {
    $username = $_SESSION['admin'];
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
} else {
    $librarian_id = $_SESSION['librarian_id'];
    $stmt = $conn->prepare("SELECT * FROM tbl_librarians WHERE librarian_id = ?");
    $stmt->bind_param("i", $librarian_id);
}

$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// 🧠 Handle Update Request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($role === 'admin') {
        $new_username = trim($_POST['username']);

        $update = $conn->prepare("UPDATE admin SET username = ?, email = ?, profile_pic = ? WHERE username = ?");
        $update->bind_param("ssss", $new_username, $email, $username);
    } else {
        $fullname = trim($_POST['fullname']);
        $section = trim($_POST['section']);

        $update = $conn->prepare("UPDATE tbl_librarians SET fullname = ?, section = ?, email = ?, profile_pic = ? WHERE librarian_id = ?");
        $update->bind_param("ssssi", $fullname, $section, $librarian_id);
    }

    if ($update->execute()) {
        $message = "✅ Profile updated successfully!";
    } else {
        $message = "❌ Error updating profile: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Update Profile | CEIT Thesis Hub</title>
    <link rel="icon" type="image/png" href="pictures/Logo.png" />
    <link rel="stylesheet" href="style.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body>
    <header class="main-header">
        <div class="header-left">
            <span class="menu-icon">☰</span>
            <h1>CEIT Thesis Hub</h1>
        </div>
        <div class="header-right">
            <h2>Update Profile</h2>
            <div class="header-logo">
                <img src="pictures/Logo.png" alt="CEIT Logo" width="90" height="60" />
            </div>
        </div>
    </header>

    <div class="container">
        <?php include 'sidebar.php'; ?>
        <main class="settings-page">
            <h2>Profile Settings</h2>
            <hr class="divider" />

            <?php if (!empty($message)) echo "<p style='color:green;'>$message</p>"; ?>

            <form method="POST" enctype="multipart/form-data" class="profile-form">

                <?php if ($role === 'admin'): ?>
                    <div class="form-group">
                        <label>Username:</label>
                        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>
                <?php else: ?>
                    <div class="form-group">
                        <label>Full Name:</label>
                        <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Section:</label>
                        <input type="text" name="section" value="<?= htmlspecialchars($user['section']) ?>" required>
                    </div>
                <?php endif; ?>

                <button type="submit" class="login-btn">Save Changes</button>
            </form>
        </main>
    </div>
</body>

</html>