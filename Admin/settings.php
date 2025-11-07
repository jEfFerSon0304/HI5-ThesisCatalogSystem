<?php
include "../PHP/db_connect.php";
session_start();
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}

$role = $_SESSION['role'];
$displayName = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : $_SESSION['admin'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Settings | CEIT Thesis Hub</title>
    <link rel="icon" type="image/png" href="pictures/Logo.png">
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
            <h2>Settings</h2>
            <div class="header-logo">
                <img src="pictures/Logo.png" alt="CEIT Logo" width="90" height="60">
            </div>
        </div>
    </header>

    <div class="container">
        <?php include 'sidebar.php'; ?>

        <main class="settings-page">
            <section class="welcome-section">
                <h2>Welcome, <?php echo htmlspecialchars($displayName); ?>!</h2>
                <p class="date"><?php echo strtoupper(date('M d, Y | l, h:i A')); ?></p>
                <hr class="divider" />
            </section>

            <?php if ($role === 'admin'): ?>
                <!-- SUPER ADMIN SETTINGS -->
                <section class="settings-category">
                    <h3>👤 Profile Settings</h3>
                    <ul>
                        <li><a href="update-profile.php">Edit Profile</a></li>
                    </ul>
                </section>

                <section class="settings-category">
                    <h3>🛠️ System Management</h3>
                    <ul>
                        <li><a href="system-management.php">Change Library Open Hours</a></li>
                        <li><a href="#">Backup Database (Manual or One-Click)</a></li>
                        <li><a href="#">Reset All Borrowing Requests</a></li>
                    </ul>
                </section>

                <section class="settings-category">
                    <h3>🔒 Security</h3>
                    <ul>
                        <li><a href="#">Set Password Policy</a></li>
                        <li><a href="#">View Login Activity Logs</a></li>
                    </ul>
                </section>

            <?php else: ?>
                <!-- LIBRARIAN SETTINGS -->
                <section class="settings-category">
                    <h3>👤 Profile Settings</h3>
                    <ul>
                        <li><a href="update-profile.php">Edit Profile</a></li>
                    </ul>
                </section>

                <section class="settings-category">
                    <h3>🔔 Notification Settings</h3>
                    <ul>
                        <li><a href="#">Toggle Sound or Pop-up Notifications</a></li>
                    </ul>
                </section>

                <section class="settings-category">
                    <h3>📘 Borrowing Rules Overview</h3>
                    <ul>
                        <li><a href="#">View Library Hours</a></li>
                        <li><a href="#">View Current Borrowing Policy</a></li>
                    </ul>
                </section>
            <?php endif; ?>
        </main>
    </div>

    <script src="script.js"></script>

</body>

</html>