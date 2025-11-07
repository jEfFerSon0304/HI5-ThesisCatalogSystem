<?php
// ✅ Always include this at the top of the file using this sidebar:
// session_start();
// $role = $_SESSION['role'];

$currentPage = basename($_SERVER['PHP_SELF']);
?>

<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Safely get $role from the session if not already defined
if (!isset($role)) {
    $role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>


<aside class="sidebar">
    <nav>
        <!-- DASHBOARD (visible to all) -->
        <a href="dashboard.php" class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
            <img src="pictures/dashboard.png" width="30" height="30"> Dashboard
        </a>

        <!-- SHARED PAGE (admin + librarian) -->
        <a href="borrowing-request.php" class="<?= $currentPage === 'borrowing-request.php' ? 'active' : '' ?>">
            <img src="pictures/request.png" width="30" height="30"> Requests
        </a>

        <!-- SUPER ADMIN FEATURES -->
        <?php if ($role === 'admin') { ?>
            <a href="manage-thesis.php" class="<?= $currentPage === 'manage-thesis.php' ? 'active' : '' ?>">
                <img src="pictures/thesis.png" width="30" height="30"> Manage Thesis
            </a>

            <a href="manage-librarians.php" class="<?= $currentPage === 'manage-librarians.php' ? 'active' : '' ?>">
                <img src="pictures/user.png" width="30" height="30"> Manage Librarians
            </a>


        <?php } ?>

        <a href="settings.php" class="<?= $currentPage === 'settings.php' ? 'active' : '' ?>">
            <img src="pictures/setting.png" width="30" height="30"> Settings
        </a>

    </nav>

    <!-- LOGOUT -->
    <form action="logout.php" method="post">
        <button id="logoutBtn" class="logout">Logout</button>
    </form>
</aside>