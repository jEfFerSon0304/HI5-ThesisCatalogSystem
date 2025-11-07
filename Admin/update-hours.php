<?php
include "../PHP/db_connect.php";
session_start();

if ($_SESSION['role'] !== 'admin') {
    die("Unauthorized access");
}

$newHours = trim($_POST['hours']);
if (empty($newHours)) {
    die("Please provide library hours.");
}

$stmt = $conn->prepare("UPDATE tbl_system_settings SET setting_value = ? WHERE setting_key = 'library_hours'");
$stmt->bind_param("s", $newHours);
if ($stmt->execute()) {
    echo "<script>alert('Library hours updated successfully!'); window.location.href='system-management.php';</script>";
} else {
    echo "<script>alert('Failed to update.'); history.back();</script>";
}
$stmt->close();
$conn->close();
