<?php
// ✅ Always include this at the top of the file using this sidebar:
// session_start();
// $role = $_SESSION['role'];

$currentPage = basename($_SERVER['PHP_SELF']);
?>

<aside class="sidebar">
    <nav>
        <!-- DASHBOARD (visible to all) -->
        <a href="dashboard.php" class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
            <img src="pictures/DASHBOARD.png" width="30" height="30"> Dashboard
        </a>

        <!-- SUPER ADMIN FEATURES -->
        <?php if ($role === 'admin') { ?>
            <a href="manage-thesis.php" class="<?= $currentPage === 'manage-thesis.php' ? 'active' : '' ?>">
                <img src="pictures/MANAGE.png" width="30" height="30"> Manage Thesis
            </a>

            <a href="manage-librarians.php" class="<?= $currentPage === 'manage-librarians.php' ? 'active' : '' ?>">
                <img src="pictures/user.png" width="30" height="30"> Manage Librarians
            </a>
        <?php } ?>

        <!-- SHARED PAGE (admin + librarian) -->
        <a href="borrowing-request.php" class="<?= $currentPage === 'borrowing-request.php' ? 'active' : '' ?>">
            <img src="pictures/REQUEST.png" width="30" height="30"> Requests
        </a>
    </nav>

    <!-- LOGOUT -->
    <form action="logout.php" method="post">
        <button id="logoutBtn" class="logout">Logout</button>
    </form>
</aside>