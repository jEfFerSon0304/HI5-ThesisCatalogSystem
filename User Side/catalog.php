<?php include "../PHP/db_connect.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CEIT Thesis Hub | Catalog</title>
    <link rel="icon" type="image/png" href="user-pictures/logo.png">
    <link rel="stylesheet" href="user-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body>
    <nav>
        <div class="logo-section">
            <img src="user-pictures/logo.png" class="logo-circle">
            <div class="title">CEIT Thesis Hub</div>
        </div>
        <div class="nav-links">
            <a href="../home.html">HOME</a>
            <a href="catalog.php" class="active">CATALOG</a>
            <a href="../Admin/index.php">ON DUTY?</a>
        </div>
    </nav>

    <main class="catalog-main">
        <section class="catalog-intro">
            <h2 class="catalog-title">CEIT Thesis Hub</h2>
            <p class="catalog-subtitle">Discover and explore academic research</p>

            <!-- SEARCH BAR -->
            <div class="catalog-search-box">
                <input
                    type="text"
                    id="searchInput"
                    placeholder="Search thesis by title, author, or keyword..."
                    class="catalog-search-input" />
                <button type="button" class="catalog-search-btn" onclick="loadResults()">Search</button>
            </div>

            <!-- FILTER DROPDOWN -->
            <div class="catalog-filter">
                <label for="catalog-dept-filter">Filter:</label>
                <select id="catalog-dept-filter" onchange="loadResults()">
                    <option value="">All Departments</option>
                    <option value="Information Technology">Information Technology</option>
                    <option value="Civil Engineering">Civil Engineering</option>
                    <option value="Electrical Engineering">Electrical Engineering</option>
                </select>
            </div>
        </section>

        <!-- TABLE SECTION -->
        <!-- TABLE SECTION -->
        <section class="catalog-table-section">
            <div class="table-wrapper">
                <table class="catalog-table" id="catalogTable">
                    <thead>
                        <tr>
                            <th style="width: 40%;">TITLE</th>
                            <th style="width: 25%;">DEPARTMENT</th>
                            <th style="width: 10%;">YEAR</th>
                            <th style="width: 10%;">STATUS</th>
                            <th style="width: 15%;">ACTION</th>
                        </tr>
                    </thead>
                    <tbody id="catalogResults">
                        <!-- Results will load here -->
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION (bottom only) -->
            <div class="pagination-container">
                <button id="prevPage" disabled>&lt;</button>
                <span>Page <span id="currentPage">1</span> of <span id="totalPages">1</span></span>
                <button id="nextPage">&gt;</button>
            </div>
        </section>


        <footer>
            <img src="user-pictures/logo.png" class="footer-logo">
            <h3>PLV CEIT THESIS CATALOG</h3>
            <div class="footer-info">
                <p><img src="user-pictures/location.png" class="footer-info-logo"> 3rd Floor, CEIT Building, Main PLV Campus, Tongco St., Maysan, Valenzuela City</p>
                <p><img src="user-pictures/email.png" class="footer-info-logo"> loremipsum@plv.edu.ph</p>
                <p><img src="user-pictures/world-wide-web.png" class="footer-info-logo"> plv.edu.ph</p>
            </div>
            <div class="copyright">Copyright © 2025</div>
        </footer>
    </main>

    <script>
        // Load thesis dynamically
        function loadResults() {
            const search = document.getElementById('searchInput').value.trim();
            const department = document.getElementById('catalog-dept-filter').value;

            fetch(`fetch_thesis.php?search=${encodeURIComponent(search)}&department=${encodeURIComponent(department)}`)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('catalogResults').innerHTML = html;
                    highlightSearch(search);
                });
        }

        // Highlight matches
        function highlightSearch(keyword) {
            if (!keyword) return;
            const regex = new RegExp(`(${keyword})`, 'gi');
            document.querySelectorAll('#catalogResults td:first-child').forEach(cell => {
                cell.innerHTML = cell.textContent.replace(regex, `<span class='highlight'>$1</span>`);
            });
        }

        // Auto-load results on page load
        document.addEventListener('DOMContentLoaded', loadResults);
        // Live search (instant typing)
        document.getElementById('searchInput').addEventListener('keyup', () => loadResults());

        const rowsPerPage = 30;
        let currentPage = 1;
        let totalPages = 1;
        let allRows = [];

        // Load thesis dynamically
        function loadResults() {
            const search = document.getElementById('searchInput').value.trim();
            const department = document.getElementById('catalog-dept-filter').value;

            fetch(`fetch_thesis.php?search=${encodeURIComponent(search)}&department=${encodeURIComponent(department)}`)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('catalogResults').innerHTML = html;
                    highlightSearch(search);

                    // After loading, rebuild pagination
                    const tableRows = Array.from(document.querySelectorAll('#catalogResults tr'));
                    allRows = tableRows;
                    currentPage = 1;
                    renderPage();
                });
        }

        // Highlight matches
        function highlightSearch(keyword) {
            if (!keyword) return;
            const regex = new RegExp(`(${keyword})`, 'gi');
            document.querySelectorAll('#catalogResults td:first-child').forEach(cell => {
                cell.innerHTML = cell.textContent.replace(regex, `<span class='highlight'>$1</span>`);
            });
        }

        // Pagination render
        function renderPage() {
            const tableBody = document.getElementById('catalogResults');
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            const pageRows = allRows.slice(start, end);

            tableBody.innerHTML = "";
            pageRows.forEach(row => tableBody.appendChild(row));

            totalPages = Math.ceil(allRows.length / rowsPerPage) || 1;
            document.getElementById("currentPage").textContent = currentPage;
            document.getElementById("totalPages").textContent = totalPages;

            document.getElementById("prevPage").disabled = currentPage === 1;
            document.getElementById("nextPage").disabled = currentPage === totalPages;
        }

        // Page navigation
        document.getElementById("prevPage").addEventListener("click", () => {
            if (currentPage > 1) {
                currentPage--;
                renderPage();
            }
        });

        document.getElementById("nextPage").addEventListener("click", () => {
            if (currentPage < totalPages) {
                currentPage++;
                renderPage();
            }
        });

        // Auto-load results on page load
        document.addEventListener('DOMContentLoaded', loadResults);

        // Live search
        document.getElementById('searchInput').addEventListener('keyup', loadResults);
    </script>
</body>

</html>