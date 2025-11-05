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
    <title>Borrowing Requests</title>
    <link rel="icon" type="image/png" href="pictures/Logo.png" />
    <link rel="stylesheet" href="style.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
</head>

<body>
    <header class="main-header">
        <div class="header-left">
            <span class="menu-icon">☰</span>
            <h1>CEIT Thesis Hub</h1>
        </div>
        <div class="header-right">
            <h2>Admin Dashboard</h2>
            <div class="header-logo">
                <img src="pictures/Logo.png" alt="CEIT Logo" width="90" height="60" />
            </div>
        </div>
    </header>

    <div class="container">
        <?php include 'sidebar.php'; ?>

        <main>
            <section class="request-header">
                <h2>Borrowing Requests</h2>
            </section>

            <section class="request-table">
                <table>
                    <thead>
                        <tr>
                            <th>Request #</th>
                            <th>Student Name</th>
                            <th>Thesis Title</th>
                            <th>Department</th>
                            <th>Date Requested</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT r.*, t.title, t.author, t.department, t.year 
                                FROM tbl_borrow_requests r 
                                JOIN tbl_thesis t ON r.thesis_id = t.thesis_id 
                                ORDER BY r.request_date DESC";

                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                // Simplify for table display
                                $status_display = $row['status'];
                                if (stripos($status_display, 'complete') !== false) {
                                    $status_display = 'Complete';
                                }

                                $json_data = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');

                                echo "
                                <tr>
                                    <td>{$row['request_number']}</td>
                                    <td>{$row['student_name']}</td>
                                    <td>{$row['title']}</td>
                                    <td>{$row['department']}</td>
                                    <td>{$row['request_date']}</td>
                                    <td>{$status_display}</td>
                                    <td><button class='view-btn' data-row='{$json_data}' onclick='openModal(this)'>View</button></td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No borrowing requests yet.</td></tr>";
                        }

                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

    <!-- VIEW MODAL -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <h3>Request Details</h3>
            <div id="modal-details"></div>
            <div class="actions" id="modal-actions"></div>
        </div>
    </div>

    <script>
        let currentRequest = null;

        function openModal(button) {
            const data = JSON.parse(button.getAttribute('data-row'));
            currentRequest = data;

            const details = document.getElementById('modal-details');
            const actions = document.getElementById('modal-actions');

            let statusClass = '';
            if (data.status === 'Complete - Returned') statusClass = 'status-returned';
            else if (data.status === 'Complete - Rejected') statusClass = 'status-rejected';

            details.innerHTML = `
                <div class="detail-item"><strong>Request #:</strong> ${data.request_number}</div>
                <div class="detail-item"><strong>Student Name:</strong> ${data.student_name}</div>
                <div class="detail-item"><strong>Student No.:</strong> ${data.student_no}</div>
                <div class="detail-item"><strong>Course & Section:</strong> ${data.course_section}</div>
                <hr>
                <div class="detail-item"><strong>Thesis Title:</strong> ${data.title}</div>
                <div class="detail-item"><strong>Author(s):</strong> ${data.author}</div>
                <div class="detail-item"><strong>Department:</strong> ${data.department}</div>
                <div class="detail-item"><strong>Year:</strong> ${data.year}</div>
                <div class="detail-item"><strong>Date Requested:</strong> ${data.request_date}</div>
                <div class="detail-item ${statusClass}"><strong>Current Status:</strong> ${data.status}</div>
            `;

            let btns = '';
            if (data.status === 'Pending') {
                btns += `<button class="status-btn approve" onclick="updateStatus(${data.request_id}, 'Approved')">Approve</button>
                         <button class="status-btn reject" onclick="updateStatus(${data.request_id}, 'Rejected')">Reject</button>`;
            } else if (data.status === 'Approved') {
                btns += `<button class="status-btn return" onclick="updateStatus(${data.request_id}, 'Returned')">Mark as Returned</button>`;
            } else if (data.status === 'Returned') {
                btns += `<button class="status-btn complete" onclick="updateStatus(${data.request_id}, 'Complete')">Mark as Complete</button>`;
            } else if (data.status === 'Rejected') {
                btns += `<button class="status-btn complete" onclick="updateStatus(${data.request_id}, 'Complete')">Mark as Complete</button>`;
            } else {
                btns = `<p style="color:gray;">No further actions available.</p>`;
            }

            actions.innerHTML = btns;
            document.getElementById('viewModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('viewModal').style.display = 'none';
        }

        function updateStatus(requestId, newStatus) {
            if (!confirm("Are you sure you want to mark this as " + newStatus + "?")) return;
            fetch('update-request-status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'request_id=' + requestId + '&new_status=' + newStatus
                })
                .then(res => res.text())
                .then(msg => {
                    alert(msg);
                    location.reload();
                });
        }

        window.onclick = function(event) {
            const modal = document.getElementById('viewModal');
            if (event.target === modal) closeModal();
        }
    </script>
</body>

</html>