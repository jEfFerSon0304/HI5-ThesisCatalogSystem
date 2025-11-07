<?php
include "../PHP/db_connect.php";
session_start();
date_default_timezone_set('Asia/Manila');

// Security check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Fetch existing open hours if table exists
$currentHours = "Not set";
$tableExists = $conn->query("SHOW TABLES LIKE 'tbl_system_settings'");
if ($tableExists && $tableExists->num_rows > 0) {
    $res = $conn->query("SELECT setting_value FROM tbl_system_settings WHERE setting_key = 'library_hours'");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $currentHours = $row['setting_value'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Management | CEIT Thesis Hub</title>
    <link rel="icon" type="image/png" href="pictures/Logo.png">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body>
    <header class="main-header">
        <div class="header-left">
            <span class="menu-icon">☰</span>
            <h1>CEIT Thesis Hub</h1>
        </div>
        <div class="header-right">
            <h2>System Management</h2>
            <div class="header-logo">
                <img src="pictures/Logo.png" alt="CEIT Logo" width="90" height="60">
            </div>
        </div>
    </header>

    <div class="container">
        <?php include 'sidebar.php'; ?>

        <main class="settings-page">
            <section class="welcome-section">
                <h2>System Management</h2>
                <p class="date"><?php echo strtoupper(date('M d, Y | l, h:i A')); ?></p>
                <hr class="divider" />
            </section>

            <!-- Alert -->
            <?php if (isset($_SESSION['msg'])): ?>
                <div class="alert-box"><?= htmlspecialchars($_SESSION['msg']); ?></div>
                <?php unset($_SESSION['msg']); ?>
            <?php endif; ?>

            <!-- Change Library Hours -->
            <!-- Change Library Hours -->
            <section class="settings-category">
                <h3>🕓 Change Library Open Hours</h3>

                <form action="update-hours.php" method="POST" class="settings-form">
                    <div class="time-inputs">

                        <?php
                        // Fetch open & close times from database if they exist
                        $open_time = "";
                        $close_time = "";

                        $tableExists = $conn->query("SHOW TABLES LIKE 'tbl_system_settings'");
                        if ($tableExists && $tableExists->num_rows > 0) {
                            $res = $conn->query("SELECT setting_value FROM tbl_system_settings WHERE setting_key = 'library_open_time'");
                            if ($res && $res->num_rows > 0) {
                                $open_time = $res->fetch_assoc()['setting_value'];
                            }
                            $res = $conn->query("SELECT setting_value FROM tbl_system_settings WHERE setting_key = 'library_close_time'");
                            if ($res && $res->num_rows > 0) {
                                $close_time = $res->fetch_assoc()['setting_value'];
                            }
                        }
                        ?>

                        <input type="time" name="open_time" value="<?= htmlspecialchars($open_time) ?>" required>
                        <span class="time-separator">to</span>
                        <input type="time" name="close_time" value="<?= htmlspecialchars($close_time) ?>" required>
                    </div>

                    <button type="submit">💾 Save Changes</button>
                </form>
            </section>


            <!-- Backup Database -->
            <section class="settings-category">
                <h3>💾 Backup Database</h3>
                <form action="backup-database.php" method="POST">
                    <button type="submit">⬇️ Download Backup</button>
                </form>
            </section>

            <!-- Reset Borrow Requests -->
            <section class="settings-category">
                <h3>🧹 Reset All Borrowing Requests</h3>
                <form action="reset-borrowing.php" method="POST" onsubmit="return confirm('Are you sure? This cannot be undone.')">
                    <button type="submit">⚠️ Reset Requests</button>
                </form>
            </section>
        </main>
    </div>
</body>

</html>