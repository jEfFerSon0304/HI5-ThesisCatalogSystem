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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard</title>
    <link rel="icon" type="image/png" href="pictures/Logo.png">
    <link rel="stylesheet" href="style.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body>
    <!-- HEADER -->
    <header class="main-header">
        <div class="header-left">
            <span class="menu-icon">☰</span>
            <h1>CEIT Thesis Hub</h1>
        </div>
        <div class="header-right">
            <h2>Admin Dashboard</h2>
            <div class="header-logo"><img src="pictures/Logo.png" alt="CEIT Logo" width="90" height="60"></div>
        </div>
    </header>

    <div class="container">
        <?php include 'sidebar.php'; ?>

        <!-- MAIN CONTENT -->
        <main>
            <?php
            // 🧮 COUNTERS
            $total_thesis = $conn->query("SELECT COUNT(*) AS total FROM tbl_thesis")->fetch_assoc()['total'];
            $approved = $conn->query("SELECT COUNT(*) AS total FROM tbl_borrow_requests WHERE status = 'Approved'")->fetch_assoc()['total'];
            $pending = $conn->query("SELECT COUNT(*) AS total FROM tbl_borrow_requests WHERE status = 'Pending'")->fetch_assoc()['total'];
            $borrowed = $conn->query("SELECT COUNT(*) AS total FROM tbl_borrow_requests WHERE status = 'Returned' OR status = 'Approved'")->fetch_assoc()['total'];
            ?>

            <section class="welcome-section">
                <h2>Welcome! Admin</h2>
                <p class="date"><?php echo strtoupper(date('M d, Y | l, h:i A')); ?></p>
                <hr class="divider" />
            </section>

            <!-- Counters -->
            <section class="dashboard-counters">
                <div class="counter-card">
                    <div class="icon-box"><img src="pictures/TOTAL.png" width="50" height="50"></div>
                    <div class="counter-details">
                        <span class="counter-title">Total Thesis</span>
                        <span class="counter-value"><?php echo $total_thesis; ?></span>
                    </div>
                </div>

                <div class="counter-card">
                    <div class="icon-box"><img src="pictures/APPROVED.png" width="50" height="50"></div>
                    <div class="counter-details">
                        <span class="counter-title">Approved</span>
                        <span class="counter-value"><?php echo $approved; ?></span>
                    </div>
                </div>

                <div class="counter-card">
                    <div class="icon-box"><img src="pictures/PENDING.png" width="50" height="50"></div>
                    <div class="counter-details">
                        <span class="counter-title">Pending Request</span>
                        <span class="counter-value"><?php echo $pending; ?></span>
                    </div>
                </div>

                <div class="counter-card">
                    <div class="icon-box"><img src="pictures/BORROWED.png" width="50" height="50"></div>
                    <div class="counter-details">
                        <span class="counter-title">Borrowed</span>
                        <span class="counter-value"><?php echo $borrowed; ?></span>
                    </div>
                </div>
            </section>

            <!-- Recent Requests -->
            <h3 style="margin-top: 40px; color: #0A3D91;">Recent Requests</h3>
            <section class="recent-requests">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 10%;">Request #</th>
                            <th style="width: 15%;">Student Name</th>
                            <th style="width: 35%;">Thesis Title</th>
                            <th style="width: 15%;">Date Requested</th>
                            <th style="width: 15%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recent = $conn->query("
                SELECT r.*, t.title, t.department 
                FROM tbl_borrow_requests r
                JOIN tbl_thesis t ON r.thesis_id = t.thesis_id
                ORDER BY r.request_date DESC
                LIMIT 5
              ");
                        if ($recent && $recent->num_rows > 0) {
                            while ($row = $recent->fetch_assoc()) {
                                $statusColor = "#333";
                                if ($row['status'] === "Pending") $statusColor = "#f39c12";
                                elseif ($row['status'] === "Approved") $statusColor = "#27ae60";
                                elseif (strpos($row['status'], "Complete") !== false) $statusColor = "#2980b9";
                                elseif ($row['status'] === "Rejected") $statusColor = "#c0392b";

                                echo "
                    <tr>
                      <td>{$row['request_number']}</td>
                      <td>{$row['student_name']}</td>
                      <td>{$row['title']}</td>
                      <td>" . date('Y-m-d', strtotime($row['request_date'])) . "</td>
                      <td style='color:$statusColor; font-weight:600;'>{$row['status']}</td>
                    </tr>
                  ";
                            }
                        } else {
                            echo "<tr><td colspan='6' style='text-align:center; color:gray;'>No recent requests found.</td></tr>";
                        }
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
    <script src="script.js"></script>
</body>

</html>