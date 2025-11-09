<?php
include "../PHP/db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $thesis_id = $_POST['thesis_id'];
    $student_name = $_POST['student_name'];
    $student_no = $_POST['student_no'];
    $course_section = $_POST['course_section'];

    // Generate unique 3-digit request number (e.g. #123)
    do {
        $random_num = rand(100, 999);
        $formatted_number = "#" . $random_num;

        $check = $conn->query("SELECT * FROM tbl_borrow_requests WHERE request_number = '$formatted_number'");
    } while ($check->num_rows > 0);

    // Insert the request
    $sql = "INSERT INTO tbl_borrow_requests (thesis_id, request_number, student_name, student_no, course_section)
            VALUES ('$thesis_id', '$formatted_number', '$student_name', '$student_no', '$course_section')";

    if ($conn->query($sql) === TRUE) {
        // Fetch related thesis info
        $thesis = $conn->query("SELECT * FROM tbl_thesis WHERE thesis_id = $thesis_id")->fetch_assoc();

        // Show confirmation preview
?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Request Confirmation</title>
            <link rel="icon" type="image/png" href="user-pictures/logo.png">
            <link rel="stylesheet" href="user-style.css">

            <style>
                :root {
                    --primary-blue: #0a3d91;
                    --hover-blue: #134dbb;
                    --accent-blue: #b3e5fc;
                    --bg-light: #f4f6f9;
                    --text-dark: #1a1a1a;
                    --text-light: #f5f5f5;
                    --white: #ffffff;
                }
            </style>
        </head>

        <body>
            <div class="confirmation-box">
                <div class="success-icon">
                    <svg viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="45" stroke="#2e7d32" stroke-width="5" fill="none" />
                        <path d="M30 52 L45 67 L70 40" stroke="#2e7d32" stroke-width="6" fill="none" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <h2>Request Submitted Successfully!</h2>
                <p class="request-number">Request Number: <?php echo $formatted_number; ?></p>

                <div class="preview-info">
                    <h4>Student Information</h4>
                    <div class="label">Student Name</div>
                    <div class="value"><?php echo htmlspecialchars($student_name); ?></div>

                    <div class="label">Student No.</div>
                    <div class="value"><?php echo htmlspecialchars($student_no); ?></div>

                    <div class="label">Course & Section</div>
                    <div class="value"><?php echo htmlspecialchars($course_section); ?></div>

                    <hr>

                    <h4>Thesis Information</h4>
                    <div class="label">Thesis Title</div>
                    <div class="value"><?php echo htmlspecialchars($thesis['title']); ?></div>

                    <div class="label">Author(s)</div>
                    <div class="value"><?php echo htmlspecialchars($thesis['author']); ?></div>

                    <div class="label">Department</div>
                    <div class="value"><?php echo htmlspecialchars($thesis['department']); ?></div>

                    <div class="label">Year</div>
                    <div class="value"><?php echo htmlspecialchars($thesis['year']); ?></div>
                </div>


                <form action="catalog.php" method="get">
                    <button type="submit" class="ok-btn">OK</button>
                </form>
            </div>
        </body>

        </html>
<?php
    } else {
        echo "<script>alert('Error submitting request: " . $conn->error . "'); window.location='catalog.php';</script>";
    }

    $conn->close();
}
?>