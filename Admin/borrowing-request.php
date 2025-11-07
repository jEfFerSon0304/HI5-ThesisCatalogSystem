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

            <section class="request-controls">
                <div class="controls-left">
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="Search student or thesis title..." />
                    </div>
                    <div class="filter-box">
                        <select id="statusFilter">
                            <option value="all">All Status</option>
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Returned">Returned</option>
                            <option value="Rejected">Rejected</option>
                            <option value="Complete">Complete</option>
                        </select>
                    </div>
                </div>

                <div class="controls-right">
                    <div class="pagination">
                        <button id="prevPage">&lt;</button>
                        <span>Page <span id="currentPage">1</span> of <span id="totalPages">1</span></span>
                        <button id="nextPage">&gt;</button>
                    </div>
                </div>
            </section>

            <section class="request-table">
                <table id="requestTable">
                    <thead>
                        <tr>
                            <th style="width: 10%;">Request #</th>
                            <th style="width: 15%;">Student Name</th>
                            <th style="width: 35%;">Thesis Title</th>
                            <th style="width: 15%;">Department</th>
                            <th style="width: 15%;">Date Requested</th>
                            <th style="width: 10%;">Status</th>
                            <th style="width: 10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php
                        $sql = "SELECT r.*, t.title, t.author, t.department, t.year, r.librarian_name 
                    FROM tbl_borrow_requests r 
                    JOIN tbl_thesis t ON r.thesis_id = t.thesis_id 
                    ORDER BY r.request_date DESC";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $status_display = $row['status'];
                                if (stripos($status_display, 'complete') !== false) {
                                    $status_display = 'Complete';
                                }

                                $json_data = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');

                                echo "
                    <tr>
                        <td >{$row['request_number']}</td>
                        <td>{$row['student_name']}</td>
                        <td>{$row['title']}</td>
                        <td>{$row['department']}</td>
                        <td>" . date('Y-m-d', strtotime($row['request_date'])) . "</td>
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
    <div id="viewModal" class="modal-1" style="display: none;">
        <div class="modal-content-1">
            <span class="modal-close-1" onclick="closeModal()">&times;</span>
            <h3>Request Details</h3>
            <div id="modal-details"></div>
            <div class="actions" id="modal-actions"></div>
        </div>
    </div>

    <script>
        // Sidebar toggle
        const menuIcon = document.querySelector(".menu-icon");
        const sidebar = document.querySelector(".sidebar");
        const container = document.querySelector(".container");

        menuIcon.addEventListener("click", () => {
            sidebar.classList.toggle("hidden");
            container.classList.toggle("full");
            menuIcon.classList.toggle("active");

            // Optional: change icon to "X"
            if (menuIcon.textContent === "☰") {
                menuIcon.textContent = "✖";
            } else {
                menuIcon.textContent = "☰";
            }
        });

        // Pagination + Filter
        let currentPage = 1;
        const rowsPerPage = 10;
        const tableBody = document.getElementById("tableBody");
        const allRows = Array.from(tableBody.querySelectorAll("tr"));
        const searchInput = document.getElementById("searchInput");
        const statusFilter = document.getElementById("statusFilter");
        const prevPage = document.getElementById("prevPage");
        const nextPage = document.getElementById("nextPage");

        function filterAndRender() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedStatus = statusFilter.value;

            const filtered = allRows.filter(row => {
                const cols = row.querySelectorAll("td");
                const name = cols[1]?.textContent.toLowerCase() || "";
                const title = cols[2]?.textContent.toLowerCase() || "";
                const status = cols[5]?.textContent.trim() || "";
                const matchesSearch = name.includes(searchTerm) || title.includes(searchTerm);
                const matchesStatus = selectedStatus === "all" || status === selectedStatus;
                return matchesSearch && matchesStatus;
            });

            const totalPages = Math.ceil(filtered.length / rowsPerPage) || 1;
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            const pageRows = filtered.slice(start, end);

            tableBody.innerHTML = "";
            pageRows.forEach(row => tableBody.appendChild(row));

            document.getElementById("currentPage").textContent = currentPage;
            document.getElementById("totalPages").textContent = totalPages;
            prevPage.disabled = currentPage === 1;
            nextPage.disabled = currentPage === totalPages;
        }

        searchInput.addEventListener("input", () => {
            currentPage = 1;
            filterAndRender();
        });
        statusFilter.addEventListener("change", () => {
            currentPage = 1;
            filterAndRender();
        });
        prevPage.addEventListener("click", () => {
            if (currentPage > 1) {
                currentPage--;
                filterAndRender();
            }
        });
        nextPage.addEventListener("click", () => {
            const filtered = allRows.filter(row => row.style.display !== "none");
            const totalPages = Math.ceil(filtered.length / rowsPerPage) || 1;
            if (currentPage < totalPages) {
                currentPage++;
                filterAndRender();
            }
        });
        filterAndRender();

        // Modal
        window.openModal = function(button) {
            const data = JSON.parse(button.getAttribute("data-row"));
            const details = document.getElementById("modal-details");
            const actions = document.getElementById("modal-actions");
            const librarianName = data.librarian_name && data.librarian_name !== "null" ? data.librarian_name : "";

            let content = `
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
                <div class="detail-item"><strong>Status:</strong> ${data.status}</div>`;
            if (librarianName) content += `<div class="detail-item"><strong>Librarian:</strong> ${librarianName}</div>`;
            details.innerHTML = content;

            let btns = "";
            if (data.status === "Pending") {
                btns = `
                    <button class="status-btn approve" onclick="updateStatus(${data.request_id}, 'Approved')">Approve</button>
                    <button class="status-btn reject" onclick="updateStatus(${data.request_id}, 'Rejected')">Reject</button>`;
            } else if (data.status === "Approved") {
                btns = `<button class="status-btn return" onclick="updateStatus(${data.request_id}, 'Returned')">Mark as Returned</button>`;
            } else if (["Returned", "Rejected"].includes(data.status)) {
                btns = `<button class="status-btn complete" onclick="updateStatus(${data.request_id}, 'Complete')">Mark as Complete</button>`;
            } else {
                btns = `<p style="color:gray;">No further actions available.</p>`;
            }

            actions.innerHTML = btns;
            document.getElementById("viewModal").style.display = "flex";
        };

        function closeModal() {
            document.getElementById("viewModal").style.display = "none";
        }

        function updateStatus(requestId, newStatus) {
            if (!confirm("Are you sure you want to mark this as " + newStatus + "?")) return;
            fetch("update-request-status.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "request_id=" + requestId + "&new_status=" + newStatus
                })
                .then(res => res.text())
                .then(msg => {
                    alert(msg);
                    location.reload();
                })
                .catch(() => alert("Error updating status."));
        }

        window.onclick = function(event) {
            const modal = document.getElementById("viewModal");
            if (event.target === modal) closeModal();
        };
    </script>
</body>

</html>