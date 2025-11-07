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

    <style>
        .catalog-table th:last-child,
        .catalog-table td:last-child {
            width: 170px !important;
            white-space: nowrap !important;
            text-align: center !important;
        }

        .catalog-table button,
        .action-btn {
            min-width: 130px !important;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
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

            <div class="catalog-search-box">
                <input type="text" id="searchInput" placeholder="Search thesis by title, author, or keyword..." class="catalog-search-input" />
                <button type="button" class="catalog-search-btn" onclick="loadResults()">Search</button>
            </div>

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

        <section class="catalog-table-section">
            <table class="catalog-table">
                <thead>
                    <tr>
                        <th>TITLE</th>
                        <th>DEPARTMENT</th>
                        <th>YEAR</th>
                        <th>STATUS</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody id="catalogResults"></tbody>
            </table>
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

        function highlightSearch(keyword) {
            if (!keyword) return;
            const regex = new RegExp(`(${keyword})`, 'gi');
            document.querySelectorAll('#catalogResults td:first-child').forEach(cell => {
                cell.innerHTML = cell.textContent.replace(regex, `<span class='highlight'>$1</span>`);
            });
        }

        document.addEventListener('DOMContentLoaded', loadResults);
        document.getElementById('searchInput').addEventListener('keyup', () => loadResults());
    </script>
</body>

</html>
