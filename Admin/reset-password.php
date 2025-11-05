<?php
// reset_password.php
include "../PHP/db_connect.php";
date_default_timezone_set('Asia/Manila');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'msg' => 'Invalid request']);
    exit;
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$code  = isset($_POST['code']) ? trim($_POST['code']) : '';
$pass  = isset($_POST['password']) ? $_POST['password'] : '';

if (empty($email) || empty($code) || empty($pass)) {
    echo json_encode(['success' => false, 'msg' => 'Missing required fields.']);
    exit;
}

$stmt = $conn->prepare("SELECT librarian_id, reset_expiry, reset_code FROM tbl_librarians WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows !== 1) {
    echo json_encode(['success' => false, 'msg' => 'No account found.']);
    exit;
}

$user = $res->fetch_assoc();

if ($user['reset_code'] !== $code) {
    echo json_encode(['success' => false, 'msg' => 'Incorrect code.']);
    exit;
}

if (strtotime($user['reset_expiry']) < time()) {
    echo json_encode(['success' => false, 'msg' => 'Code expired.']);
    exit;
}

// all good — update password
$hashed = password_hash($pass, PASSWORD_DEFAULT);
$upd = $conn->prepare("UPDATE tbl_librarians SET password = ?, reset_code = NULL, reset_expiry = NULL WHERE librarian_id = ?");
$upd->bind_param("si", $hashed, $user['librarian_id']);
$upd->execute();

echo json_encode(['success' => true, 'msg' => 'Password updated.']);
exit;
