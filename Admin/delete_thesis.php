<?php
include "../PHP/db_connect.php";
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $sql = "DELETE FROM tbl_thesis WHERE thesis_id = $id";
    echo ($conn->query($sql)) ? "🗑️ Thesis deleted successfully!" : "❌ Error deleting: " . $conn->error;
}
$conn->close();
