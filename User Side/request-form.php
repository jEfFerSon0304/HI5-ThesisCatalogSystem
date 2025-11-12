<?php
include "../PHP/db_connect.php";

// Get thesis details
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('No thesis selected!'); window.location='catalog.php';</script>";
    exit();
}

$id = intval($_GET['id']);
$query = "SELECT * FROM tbl_thesis WHERE thesis_id = $id";
$result = $conn->query($query);

if ($result->num_rows == 0) {
    echo "<script>alert('Thesis not found!'); window.location='catalog.php';</script>";
    exit();
}

$thesis = $result->fetch_assoc();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <title>CEIT Thesis Hub | Request Form</title>
    <link rel="icon" type="image/png" href="user-pictures/logo.png" />
    <link rel="stylesheet" href="user-style.css" />
</head>

<body>
    <nav>
        <div class="logo-section">
            <img src="user-pictures/logo.png" class="logo-circle" />
            <div class="title">CEIT Thesis Hub</div>
        </div>
        <div class="nav-links">
            <a href="../home.php">HOME</a>
            <a href="catalog.php" class="active">CATALOG</a>
        </div>
    </nav>

    <main class="request-container">
        <h2 class="request-title">REQUEST FORM</h2>

        <form id="requestForm" action="save-request.php" method="POST" class="request-form-box">
            <!-- Hidden thesis ID -->
            <input type="hidden" name="thesis_id" value="<?php echo $thesis['thesis_id']; ?>">

            <div class="thesis-info-box">
                <h3>Thesis Information</h3>
                <p><strong>Title</strong><span><?php echo htmlspecialchars($thesis['title']); ?></span></p><br>
                <p><strong>Author(s)</strong><span><?php echo htmlspecialchars($thesis['author']); ?></span></p><br>
                <p><strong>Department</strong><span><?php echo htmlspecialchars($thesis['department']); ?></span></p><br>
                <p><strong>Year</strong><span><?php echo htmlspecialchars($thesis['year']); ?></span></p>
            </div>

            <hr>

            <label for="student_name">Student Name</label>
            <input type="text" name="student_name" id="student_name" required />

            <label for="student_no">Student No.</label>
            <input type="text" name="student_no" id="student_no" required />

            <label for="course_section">Course & Section</label>
            <input type="text" name="course_section" id="course_section" required />

            <div class="agreement">
                <input type="checkbox" id="agreeCheckbox" onchange="toggleSubmit()" />
                <label for="agreeCheckbox">
                    I agree to the borrowing terms and conditions of the CEIT Thesis Hub.
                </label>
            </div>

            <div class="form-buttons">
                <button type="submit" id="submitBtn" class="btn-submit disabled-btn" disabled>SUBMIT REQUEST</button>
                <a href="catalog.php" class="btn-cancel">CANCEL</a>
            </div>
        </form>
    </main>

    <footer>
        <img src="user-pictures/logo.png" class="footer-logo" />
        <h3>PLV CEIT THESIS CATALOG</h3>
        <div class="footer-info">
            <p><img src="user-pictures/location.png" class="footer-info-logo" />3rd Floor, CEIT Building, Main PLV Campus, Tongco St., Maysan, Valenzuela City</p>
            <p><img src="user-pictures/email.png" class="footer-info-logo" /> loremipsum@plv.edu.ph</p>
            <p><img src="user-pictures/world-wide-web.png" class="footer-info-logo" /> plv.edu.ph</p>
        </div>
        <div class="copyright">Copyright © 2025</div>
    </footer>

    <script>
        function toggleSubmit() {
            const checkbox = document.getElementById('agreeCheckbox');
            const submitBtn = document.getElementById('submitBtn');
            if (checkbox.checked) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('disabled-btn');
            } else {
                submitBtn.disabled = true;
                submitBtn.classList.add('disabled-btn');
            }
        }
    </script>
</body>

</html>