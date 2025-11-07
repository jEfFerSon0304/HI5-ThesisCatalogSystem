<?php
include "../PHP/db_connect.php";
session_start();
date_default_timezone_set('Asia/Manila');

// 🔒 Redirect if not logged in or not admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
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
        body {
            font-family: "Poppins", sans-serif;
            background: #f6f8fa;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th,
        table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        table th {
            background: #2f3640;
            color: #fff;
            text-transform: uppercase;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .action-btn {
            padding: 6px 12px;
            margin: 2px;
            border: none;
            border-radius: 6px;
            color: #fff;
            cursor: pointer;
            transition: 0.2s;
            font-size: 13px;
        }

        .view {
            background-color: #3498db;
        }

        .view:hover {
            background-color: #2e86c1;
        }

        .divider {
            border: 0;
            height: 2px;
            background: #ddd;
            margin: 15px 0;
        }

        .filter-bar {
            margin-bottom: 15px;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .filter-bar input,
        .filter-bar select {
            padding: 7px 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 10px;
            color: #fff;
            font-weight: 600;
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

            <!-- 🔍 Filter + Sort Section -->
            <div class="filter-bar">
                <input type="text" id="searchInput" placeholder="Search name or email...">
                <select id="statusFilter">
                    <option value="">All Status</option>
                    <option value="approved">Approved</option>
                    <option value="pending">Pending</option>
                    <option value="inactive">Inactive</option>
                    <option value="rejected">Rejected</option>
                </select>
                <select id="sortBy">
                    <option value="id">Sort by ID</option>
                    <option value="name">Sort by Name</option>
                    <option value="status">Sort by Status</option>
                </select>
            </div>

            <!-- 📋 Librarians Table -->
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
                                $statusClass = "status-" . strtolower($row['status']);

                                echo "
                                    <tr>
                                        <td>{$row['librarian_id']}</td>
                                        <td>{$row['fullname']}</td>
                                        <td>{$row['email']}</td>
                                        <td>{$row['section']}</td>
                                        <td><span class='status-badge $statusClass'>{$row['status']}</span></td>
                                        <td>" . ($row['last_login'] ?? 'N/A') . "</td>
                                        <td>
                                            <button class='action-btn view' onclick=\"window.location.href='view-librarian.php?id={$row['librarian_id']}'\">View</button>
                                        </td>
                                    </tr>
                                ";
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

    <script>
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

        // Search, filter, sort
        const searchInput = document.getElementById("searchInput");
        const statusFilter = document.getElementById("statusFilter");
        const sortBy = document.getElementById("sortBy");
        const tableBody = document.querySelector("tbody");

        function filterAndSort() {
            const rows = Array.from(tableBody.querySelectorAll("tr"));
            const searchTerm = searchInput.value.toLowerCase();
            const selectedStatus = statusFilter.value;

            let filtered = rows.filter(row => {
                const cells = row.querySelectorAll("td");
                const name = cells[1].textContent.toLowerCase();
                const email = cells[2].textContent.toLowerCase();
                const status = cells[4].textContent.toLowerCase();
                const matchSearch = name.includes(searchTerm) || email.includes(searchTerm);
                const matchStatus = selectedStatus === "" || status.includes(selectedStatus);
                return matchSearch && matchStatus;
            });

            filtered.sort((a, b) => {
                const aCells = a.querySelectorAll("td");
                const bCells = b.querySelectorAll("td");
                if (sortBy.value === "name") return aCells[1].textContent.localeCompare(bCells[1].textContent);
                if (sortBy.value === "status") return aCells[4].textContent.localeCompare(bCells[4].textContent);
                return parseInt(bCells[0].textContent) - parseInt(aCells[0].textContent);
            });

            tableBody.innerHTML = "";
            filtered.forEach(row => tableBody.appendChild(row));
        }

        [searchInput, statusFilter, sortBy].forEach(el => {
            el.addEventListener("input", filterAndSort);
            el.addEventListener("change", filterAndSort);
        });
    </script>
</body>

</html>