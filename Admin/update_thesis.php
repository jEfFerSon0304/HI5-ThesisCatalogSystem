<?php
include "../PHP/db_connect.php";
session_start();

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$open_time = $_POST['open_time'];
$close_time = $_POST['close_time'];

$conn->query("CREATE TABLE IF NOT EXISTS tbl_system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT NOT NULL
)");

// Save open time
$stmt = $conn->prepare("
    INSERT INTO tbl_system_settings (setting_key, setting_value)
    VALUES ('library_open_time', ?)
    ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
");
$stmt->bind_param("s", $open_time);
$stmt->execute();

// Save close time
$stmt = $conn->prepare("
    INSERT INTO tbl_system_settings (setting_key, setting_value)
    VALUES ('library_close_time', ?)
    ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
");
$stmt->bind_param("s", $close_time);
$stmt->execute();

$stmt->close();
$conn->close();

$_SESSION['msg'] = "✅ Library hours updated: $open_time to $close_time";
header("Location: system-management.php");
exit;
