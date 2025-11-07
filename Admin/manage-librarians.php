<?php
include "../PHP/db_connect.php";
session_start();
date_default_timezone_set('Asia/Manila');

// 🔒 Redirect if not logged in
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Librarians | CEIT Thesis Hub</title>
    <link rel="icon" type="image/png" href="pictures/Logo.png">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>

    </style>
</head>

<body>
    <header class="main-header">
        <div class="header-left">
            <span class="menu-icon">☰</span>
            <h1>CEIT Thesis Hub</h1>
        </div>
        <div class="header-right">
            <h2>Manage Librarians</h2>
            <div class="header-logo"><img src="pictures/Logo.png" width="90" height="60" alt="CEIT Logo"></div>
        </div>
    </header>

    <div class="container">
        <?php include 'sidebar.php'; ?>

        <main>
            <section class="welcome-section">
                <h2>Librarian Accounts</h2>
                <p class="date"><?php echo strtoupper(date('M d, Y | l, h:i A')); ?></p>
                <hr class="divider" />
            </section>

            <section>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Section</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $conn->query("SELECT * FROM tbl_librarians ORDER BY librarian_id DESC");
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $statusColor = match ($row['status']) {
                                    'approved' => 'green',
                                    'pending' => 'orange',
                                    'inactive' => 'gray',
                                    default => 'black'
                                };

                                echo "<tr>
                  <td>{$row['librarian_id']}</td>
                  <td>{$row['fullname']}</td>
                  <td>{$row['email']}</td>
                  <td>{$row['section']}</td>
                  <td style='color:$statusColor;font-weight:600;'>{$row['status']}</td>
                  <td>" . ($row['last_login'] ?? 'N/A') . "</td>
                  <td>";

                                if ($row['status'] === 'pending') {
                                    echo "
                    <button class='action-btn approve' onclick=\"updateStatus({$row['librarian_id']}, 'approved')\">Approve</button>
                    <button class='action-btn reject' onclick=\"updateStatus({$row['librarian_id']}, 'rejected')\">Reject</button>
                  ";
                                } elseif ($row['status'] === 'approved') {
                                    echo "
                    <button class='action-btn deactivate' onclick=\"updateStatus({$row['librarian_id']}, 'inactive')\">Deactivate</button>
                  ";
                                } elseif ($row['status'] === 'inactive') {
                                    echo "
                    <button class='action-btn approve' onclick=\"updateStatus({$row['librarian_id']}, 'approved')\">Reactivate</button>
                  ";
                                }

                                echo "</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No librarian records found.</td></tr>";
                        }
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

    <script src="script.js">
        function updateStatus(id, newStatus) {
            if (!confirm(`Are you sure you want to set this account to '${newStatus}'?`)) return;

            fetch('update-librarian-status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `id=${id}&status=${newStatus}`
                })
                .then(res => res.text())
                .then(msg => {
                    alert(msg);
                    location.reload();
                })
                .catch(err => alert('Error updating status.'));
        }
    </script>
</body>

</html>