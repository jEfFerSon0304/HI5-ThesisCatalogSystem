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
    <title>Manage Thesis</title>
    <link rel="icon" type="image/png" href="pictures/Logo.png" />
    <link rel="stylesheet" href="style.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
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
            <div class="header-logo">
                <img src="pictures/Logo.png" alt="CEIT Logo" width="90" height="60" />
            </div>
        </div>
    </header>

    <div class="container">
        <?php include 'sidebar.php'; ?>


        <!-- MAIN CONTENT -->
        <main>
            <!-- TABLE SECTION -->
            <section id="thesisOverview" class="thesis-overview fade-in">
                <h2>Manage Thesis</h2>
                <button id="addThesisBtn" class="add-btn-header">📄 Add New Thesis</button>

                <!-- ✅ SEARCH + FILTER + BULK TOGGLE -->
                <!-- ✅ TABLE CONTROLS BAR -->
                <div class="table-controls-wrapper">
                    <div class="controls-left">
                        <!-- Search Bar -->
                        <input type="text" id="searchInput" placeholder="Search by title or author...">

                        <!-- Filter (with checkboxes inside dropdown) -->
                        <div class="filter-dropdown">
                            <button type="button" id="filterToggleBtn">Filter ⏷</button>
                            <div id="filterMenu" class="filter-menu hidden">
                                <label><input type="checkbox" class="filter-dept" value="Information Technology"> Information Technology</label>
                                <label><input type="checkbox" class="filter-dept" value="Civil Engineering"> Civil Engineering</label>
                                <label><input type="checkbox" class="filter-dept" value="Electrical Engineering"> Electrical Engineering</label>
                                <hr>
                                <label><input type="checkbox" class="filter-availability" value="Available"> Available</label>
                                <label><input type="checkbox" class="filter-availability" value="Unavailable"> Unavailable</label>
                            </div>
                        </div>
                    </div>

                    <div class="controls-right">
                        <!-- Pagination -->
                        <div class="pagination">
                            <button id="prevPage">&lt;</button>
                            <span>Page <span id="currentPage">1</span> of <span id="totalPages">1</span></span>
                            <button id="nextPage">&gt;</button>
                        </div>
                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th style="width: 40%;">Title</th>
                            <th style="width: 25%;">Author(s)</th>
                            <th style="width: 10%;">Year</th>
                            <th style="width: 10%;">Department</th>
                            <th style="width: 15%;">Availability</th>
                            <!-- <th>Last Updated</th> -->
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM tbl_thesis ORDER BY year DESC";
                        $result = $conn->query($query);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                // ✅ Safely encode PHP array into valid JS object string
                                $jsonData = json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG);
                        ?>
                                <tr data-id="<?= htmlspecialchars($row['thesis_id']) ?>">
                                    <td><?= htmlspecialchars($row['title']) ?></td>
                                    <td><?= htmlspecialchars($row['author']) ?></td>
                                    <td><?= htmlspecialchars($row['year']) ?></td>
                                    <td><?= htmlspecialchars($row['department']) ?></td>
                                    <td><?= htmlspecialchars($row['availability']) ?></td>
                                    <td>
                                        <button
                                            class="action-btn edit-btn"
                                            onclick='openEditModal(<?= $jsonData ?>)'>
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='7'>No thesis records found.</td></tr>";
                        }
                        $conn->close();
                        ?>
                    </tbody>

                </table>
            </section>

            <!-- ADD THESIS FORM -->
            <section id="addThesisFormSection" class="add-thesis-section hidden fade-out">
                <h3>Add New Thesis</h3>
                <form id="addThesisForm" action="add_thesis.php" method="POST">
                    <div class="form-group">
                        <label for="title">Thesis Title</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="author">Author(s)</label>
                        <input type="text" id="author" name="author" required>
                    </div>
                    <div class="form-group">
                        <label for="year">Year</label>
                        <input type="number" id="year" name="year" placeholder="YYYY" required>
                    </div>
                    <div class="form-group">
                        <label for="department">Department</label>
                        <select id="department" name="department" required>
                            <option value="">Select Department</option>
                            <option value="Information Technology">Information Technology</option>
                            <option value="Civil Engineering">Civil Engineering</option>
                            <option value="Electrical Engineering">Electrical Engineering</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="abstract">Abstract</label>
                        <textarea id="abstract" name="abstract" rows="5" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="availability">Availability</label>
                        <select id="availability" name="availability" required>
                            <option value="">Select...</option>
                            <option value="Available">Available</option>
                            <option value="Unavailable">Unavailable</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="button" id="cancelFormBtn" class="cancel-btn">CANCEL</button>
                        <button type="submit" class="add-btn">ADD THESIS</button>
                    </div>
                </form>
            </section>
        </main>
    </div>

    <!-- EDIT MODAL (unchanged) -->
    <div class="modal" id="editModal" style="display: none;">
        <div class="modal-content">
            <span class="close-modal" id="editModalCloseBtn">&times;</span>
            <h3>Edit Thesis</h3>
            <form id="editForm">
                <input type="hidden" id="edit_thesis_id" name="thesis_id">
                <div class="form-group"><label>Title</label><input type="text" id="edit_title" name="title" required></div>
                <div class="form-group"><label>Author(s)</label><input type="text" id="edit_author" name="author" required></div>
                <div class="form-group"><label>Year</label><input type="number" id="edit_year" name="year" required></div>
                <div class="form-group"><label>Department</label>
                    <select id="edit_department" name="department" required>
                        <option value="Information Technology">Information Technology</option>
                        <option value="Civil Engineering">Civil Engineering</option>
                        <option value="Electrical Engineering">Electrical Engineering</option>
                    </select>
                </div>
                <div class="form-group"><label>Availability</label>
                    <select id="edit_availability" name="availability" required>
                        <option value="Available">Available</option>
                        <option value="Unavailable">Unavailable</option>
                    </select>
                </div>
                <div class="form-group"><label>Abstract</label><textarea id="edit_abstract" name="abstract" rows="3"></textarea></div>

                <div class="modal-footer">
                    <span class="last-updated" id="lastUpdatedText">Last Updated: —</span>
                    <div>
                        <button type="submit" class="save-btn">💾 Save</button>
                        <button type="button" class="delete-btn" id="deleteBtn">🗑 Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- TOAST CONTAINER -->
    <div id="toastContainer" class="toast-container"></div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            /* =========================
               🧭 SIDEBAR TOGGLE
            ========================= */
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

            /* =========================
               📄 ADD THESIS SECTION TOGGLE
            ========================= */
            const addThesisBtn = document.getElementById("addThesisBtn");
            const addThesisFormSection = document.getElementById("addThesisFormSection");
            const cancelFormBtn = document.getElementById("cancelFormBtn");
            const thesisOverview = document.getElementById("thesisOverview");

            if (addThesisBtn && addThesisFormSection && cancelFormBtn) {
                addThesisBtn.addEventListener("click", () => {
                    thesisOverview.classList.add("hidden");
                    addThesisFormSection.classList.remove("hidden");
                });

                cancelFormBtn.addEventListener("click", () => {
                    addThesisFormSection.classList.add("hidden");
                    thesisOverview.classList.remove("hidden");
                });
            }

            /* =========================
               🔍 SEARCH, FILTER & PAGINATION
            ========================= */
            const searchInput = document.getElementById("searchInput");
            const tableBody = document.querySelector("tbody");
            const allRows = Array.from(tableBody.querySelectorAll("tr"));
            const filterToggleBtn = document.getElementById("filterToggleBtn");
            const filterMenu = document.getElementById("filterMenu");
            const filterDept = document.querySelectorAll(".filter-dept");
            const filterAvail = document.querySelectorAll(".filter-availability");
            const prevPage = document.getElementById("prevPage");
            const nextPage = document.getElementById("nextPage");
            const currentPageDisplay = document.getElementById("currentPage");
            const totalPagesDisplay = document.getElementById("totalPages");

            let currentPage = 1;
            const rowsPerPage = 10;

            // Filter dropdown toggle
            if (filterToggleBtn && filterMenu) {
                filterToggleBtn.addEventListener("click", () => {
                    filterMenu.classList.toggle("hidden");
                });

                document.addEventListener("click", (e) => {
                    if (!filterToggleBtn.contains(e.target) && !filterMenu.contains(e.target)) {
                        filterMenu.classList.add("hidden");
                    }
                });
            }

            // Apply filters and search
            function filterAndRender() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedDepts = Array.from(filterDept)
                    .filter(chk => chk.checked)
                    .map(chk => chk.value);
                const selectedAvail = Array.from(filterAvail)
                    .filter(chk => chk.checked)
                    .map(chk => chk.value);

                let filtered = allRows.filter(row => {
                    const title = row.children[0].textContent.toLowerCase();
                    const author = row.children[1].textContent.toLowerCase();
                    const dept = row.children[3].textContent;
                    const availability = row.children[4].textContent;

                    const matchesSearch = title.includes(searchTerm) || author.includes(searchTerm);
                    const matchesDept = selectedDepts.length === 0 || selectedDepts.includes(dept);
                    const matchesAvail = selectedAvail.length === 0 || selectedAvail.includes(availability);

                    return matchesSearch && matchesDept && matchesAvail;
                });

                renderTable(filtered);
            }

            function renderTable(filteredRows) {
                const totalPages = Math.ceil(filteredRows.length / rowsPerPage) || 1;
                currentPage = Math.min(currentPage, totalPages);
                const start = (currentPage - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                const pageRows = filteredRows.slice(start, end);

                tableBody.innerHTML = "";
                pageRows.forEach(row => tableBody.appendChild(row));

                currentPageDisplay.textContent = currentPage;
                totalPagesDisplay.textContent = totalPages;

                prevPage.disabled = currentPage === 1;
                nextPage.disabled = currentPage === totalPages;
            }

            if (searchInput) {
                searchInput.addEventListener("input", () => {
                    currentPage = 1;
                    filterAndRender();
                });
            }

            [...filterDept, ...filterAvail].forEach(chk => {
                chk.addEventListener("change", () => {
                    currentPage = 1;
                    filterAndRender();
                });
            });

            prevPage.addEventListener("click", () => {
                if (currentPage > 1) {
                    currentPage--;
                    filterAndRender();
                }
            });

            nextPage.addEventListener("click", () => {
                currentPage++;
                filterAndRender();
            });

            filterAndRender(); // initial load

            /* =========================
               ✏️ EDIT MODAL FUNCTIONS
            ========================= */
            const editModal = document.getElementById("editModal");
            const editModalCloseBtn = document.getElementById("editModalCloseBtn");
            const editForm = document.getElementById("editForm");
            const deleteBtn = document.getElementById("deleteBtn");
            const toastContainer = document.getElementById("toastContainer");

            window.openEditModal = function(data) {
                document.getElementById("edit_thesis_id").value = data.thesis_id;
                document.getElementById("edit_title").value = data.title;
                document.getElementById("edit_author").value = data.author;
                document.getElementById("edit_year").value = data.year;
                document.getElementById("edit_department").value = data.department;
                document.getElementById("edit_availability").value = data.availability;
                document.getElementById("edit_abstract").value = data.abstract || "";
                document.getElementById("lastUpdatedText").textContent = "Last Updated: " + (data.last_updated || "—");
                editModal.style.display = "flex";
            };

            function closeModal() {
                editModal.style.display = "none";
            }

            if (editModalCloseBtn) {
                editModalCloseBtn.addEventListener("click", closeModal);
            }

            window.addEventListener("click", (e) => {
                if (e.target === editModal) closeModal();
            });

            // Save edits
            if (editForm) {
                editForm.addEventListener("submit", (e) => {
                    e.preventDefault();

                    const formData = new FormData(editForm);
                    fetch("update_thesis.php", {
                            method: "POST",
                            body: formData
                        })
                        .then(res => res.text())
                        .then(msg => {
                            showToast(msg, "success");
                            closeModal();
                            setTimeout(() => location.reload(), 800);
                        })
                        .catch(() => showToast("Error updating thesis.", "error"));
                });
            }

            // Delete thesis
            if (deleteBtn) {
                deleteBtn.addEventListener("click", () => {
                    const thesisId = document.getElementById("edit_thesis_id").value;
                    if (!confirm("Are you sure you want to delete this thesis?")) return;

                    fetch("delete_thesis.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            },
                            body: `thesis_id=${thesisId}`
                        })
                        .then(res => res.text())
                        .then(msg => {
                            showToast(msg, "info");
                            closeModal();
                            setTimeout(() => location.reload(), 800);
                        })
                        .catch(() => showToast("Error deleting thesis.", "error"));
                });
            }

            /* =========================
               🔔 TOAST FUNCTION
            ========================= */
            function showToast(message, type = "info") {
                const toast = document.createElement("div");
                toast.className = `toast ${type}`;
                toast.textContent = message;
                toastContainer.appendChild(toast);

                setTimeout(() => {
                    toast.remove();
                }, 2500);
            }
        });
    </script>

</body>

</html>