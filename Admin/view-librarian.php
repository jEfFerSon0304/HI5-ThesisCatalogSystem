<?php
include "../PHP/db_connect.php";
session_start();
date_default_timezone_set('Asia/Manila');

// 🔒 Security check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("No librarian selected.");
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM tbl_librarians WHERE librarian_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$librarian = $result->fetch_assoc();

if (!$librarian) {
    die("Librarian not found.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Librarian | CEIT Thesis Hub</title>
    <link rel="icon" type="image/png" href="pictures/Logo.png">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: "Poppins", sans-serif;
            background: #f6f8fa;
        }

        .librarian-profile {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            width: 80%;
            max-width: 900px;
        }

        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .profile-header h2 {
            color: #0A3D91;
            font-size: 24px;
        }

        .profile-info {
            margin-top: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .profile-field {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .profile-field strong {
            color: #0A3D91;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 12px;
            color: #fff;
            text-transform: capitalize;
        }

        .status-approved {
            background-color: #4caf50;
        }

        .status-pending {
            background-color: #ff9800;
        }

        .status-inactive {
            background-color: #9e9e9e;
        }

        .status-rejected {
            background-color: #f44336;
        }

        .actions {
            margin-top: 30px;
            text-align: center;
        }

        .action-btn {
            padding: 8px 14px;
            margin: 4px;
            border: none;
            border-radius: 6px;
            color: #fff;
            cursor: pointer;
            transition: 0.2s;
            font-size: 14px;
        }

        .approve {
            background-color: #4caf50;
        }

        .reject {
            background-color: #f44336;
        }

        .deactivate {
            background-color: #9e9e9e;
        }

        .delete {
            background-color: #e74c3c;
        }

        .delete:hover {
            background-color: #c0392b;
        }

        .back-btn {
            display: inline-block;
            background: #0A3D91;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: 0.2s;
        }

        .back-btn:hover {
            background: #062d6f;
        }

        .divider {
            border: 0;
            height: 2px;
            background: #ddd;
            margin: 15px 0;
        }
    </style>
</head>

<body>
    <header class="main-header">
        <div class="header-left">
            <span class="menu-icon">☰</span>
            <h1>CEIT Thesis Hub</h1>
        </div>
        <div class="header-right">
            <h2>View Librarian</h2>
            <div class="header-logo">
                <img src="pictures/Logo.png" alt="CEIT Logo" width="90" height="60">
            </div>
        </div>
    </header>

    <div class="container">
        <?php include 'sidebar.php'; ?>

        <main class="settings-page">
            <section class="welcome-section">
                <h2>Librarian Profile</h2>
                <p class="date"><?php echo strtoupper(date('M d, Y | l, h:i A')); ?></p>
                <hr class="divider" />
            </section>

            <div class="librarian-profile">
                <div class="profile-header">
                    <h2><?= htmlspecialchars($librarian['fullname']); ?></h2>
                    <a href="manage-librarians.php" class="back-btn">← Back to Manage Librarians</a>
                </div>

                <div class="profile-info">
                    <div class="profile-field"><strong>Email:</strong> <?= htmlspecialchars($librarian['email']); ?></div>
                    <div class="profile-field"><strong>Section:</strong> <?= htmlspecialchars($librarian['section']); ?></div>
                    <div class="profile-field"><strong>Status:</strong>
                        <span class="status-badge status-<?= strtolower($librarian['status']); ?>">
                            <?= ucfirst($librarian['status']); ?>
                        </span>
                    </div>
                    <div class="profile-field"><strong>Date Created:</strong> <?= $librarian['date_created'] ?? 'N/A'; ?></div>
                    <div class="profile-field"><strong>Last Login:</strong> <?= $librarian['last_login'] ?? 'Never'; ?></div>
                </div>

                <div class="actions">
                    <?php
                    $id = $librarian['librarian_id'];
                    $status = strtolower(trim($librarian['status']));

                    if ($status === 'pending') {
                        echo "<button class='action-btn approve' onclick=\"updateStatus($id, 'approved')\">Approve</button>
                              <button class='action-btn reject' onclick=\"updateStatus($id, 'rejected')\">Reject</button>";
                    } elseif ($status === 'approved') {
                        echo "<button class='action-btn deactivate' onclick=\"updateStatus($id, 'inactive')\">Deactivate</button>";
                    } elseif ($status === 'inactive') {
                        echo "<button class='action-btn approve' onclick=\"updateStatus($id, 'approved')\">Reactivate</button>";
                    } elseif ($status === 'rejected') {
                        echo "<button class='action-btn approve' onclick=\"updateStatus($id, 'approved')\">Re-Approve</button>";
                    }

                    // 🗑️ Delete Button
                    echo "<button class='action-btn delete' onclick=\"deleteAccount($id)\">Delete Account</button>";
                    ?>
                </div>
            </div>
        </main>
    </div>

    <script>
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
                .catch(() => alert('Error updating status.'));
        }

        function deleteAccount(id) {
            if (!confirm("⚠️ Are you sure you want to permanently delete this librarian account? This action cannot be undone.")) return;
            fetch('delete-librarian.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `id=${id}`
                })
                .then(res => res.text())
                .then(msg => {
                    alert(msg);
                    window.location.href = 'manage-librarians.php';
                })
                .catch(() => alert('Error deleting librarian.'));
        }
    </script>
</body>

</html>