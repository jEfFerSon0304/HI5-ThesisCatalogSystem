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
    <div class="modal" id="editModal">
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

    <script src="script.js"></script>
</body>

</html>