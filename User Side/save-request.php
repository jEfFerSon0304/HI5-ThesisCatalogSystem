<?php
include "../PHP/db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $thesis_id = $_POST['thesis_id'];
    $student_name = $_POST['student_name'];
    $student_no = $_POST['student_no'];
    $course_section = $_POST['course_section'];

    // Generate unique request number
    do {
        $random_num = rand(100, 999);
        $formatted_number = "#" . $random_num;

        $check = $conn->query("SELECT * FROM tbl_borrow_requests WHERE request_number = '$formatted_number'");
    } while ($check->num_rows > 0);

    // Insert Request
    $sql = "INSERT INTO tbl_borrow_requests (thesis_id, request_number, student_name, student_no, course_section)
            VALUES ('$thesis_id', '$formatted_number', '$student_name', '$student_no', '$course_section')";

    if ($conn->query($sql) === TRUE) {

        // Fetch Thesis Info
        $thesis = $conn->query("SELECT * FROM tbl_thesis WHERE thesis_id = $thesis_id")->fetch_assoc();
        ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0" />
            <title>Request Confirmation</title>
            <link rel="icon" type="image/png" href="user-pictures/logo.png" />
            <link rel="stylesheet" href="user-style.css" />

            <!-- Override Styles -->
            <style>
    body {
        margin: 0 !important;
        padding: 0 !important;
        height: 100vh !important;
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        background: #f7f7f7 !important;
        font-family: Arial, sans-serif !important;
    }

    .confirmation-box {
        width: 600px !important;
        max-width: 90% !important;
        background: #ffffff !important;
        padding: 30px !important;
        border-radius: 8px !important;
        text-align: center !important;
        box-shadow: 0 0 12px rgba(0, 0, 0, 0.15) !important;
    }

    .confirmation-box h2 {
        margin-bottom: 10px !important;
    }

    .preview-info {
        text-align: left !important;
        margin-top: 15px !important;
        line-height: 1.4 !important;
    }

    .ok-btn {
        margin-top: 20px !important;
        padding: 12px 28px !important;
        background: #007bff !important;
        border: none !important;
        color: white !important;
        border-radius: 5px !important;
        cursor: pointer !important;
        font-size: 16px !important;
    }

    .ok-btn:hover {
        background: #0056b3 !important;
    }
</style>

        </head>

        <body>
            <div class="confirmation-box">
                <h2>Request Submitted Successfully!</h2>
                <p class="request-number"><strong>Request Number:</strong> <?php echo $formatted_number; ?></p>

                <div class="preview-info">
                    <p><strong>Student Name:</strong> <?php echo htmlspecialchars($student_name); ?></p>
                    <p><strong>Student No.:</strong> <?php echo htmlspecialchars($student_no); ?></p>
                    <p><strong>Course & Section:</strong> <?php echo htmlspecialchars($course_section); ?></p>
                    <hr>
                    <p><strong>Thesis Title:</strong> <?php echo htmlspecialchars($thesis['title']); ?></p>
                    <p><strong>Author(s):</strong> <?php echo htmlspecialchars($thesis['author']); ?></p>
                    <p><strong>Department:</strong> <?php echo htmlspecialchars($thesis['department']); ?></p>
                    <p><strong>Year:</strong> <?php echo htmlspecialchars($thesis['year']); ?></p>
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
