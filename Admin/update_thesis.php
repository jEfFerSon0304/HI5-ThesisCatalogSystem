<?php
include "../PHP/db_connect.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['thesis_id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $year = $_POST['year'];
    $department = $_POST['department'];
    $availability = $_POST['availability'];
    $abstract = $_POST['abstract'];

    $sql = "UPDATE tbl_thesis 
            SET title='$title', author='$author', year='$year', department='$department',
                availability='$availability', abstract='$abstract'
            WHERE thesis_id='$id'";

    echo ($conn->query($sql)) ? "✅ Thesis updated successfully!" : "❌ Error: " . $conn->error;
}
$conn->close();
