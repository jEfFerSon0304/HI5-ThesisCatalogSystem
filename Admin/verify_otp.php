<?php
include "../PHP/db_connect.php";
session_start();

$message = "";
$email = $_SESSION['pending_email'] ?? '';

if (!$email) {
    header("Location: register-librarian.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp']);
    $check = $conn->prepare("SELECT * FROM tbl_librarians WHERE email = ? AND otp_code = ?");
    $check->bind_param("ss", $email, $otp);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $conn->query("UPDATE tbl_librarians SET otp_code=NULL, is_verified=1, status='active' WHERE email='$email'");
        unset($_SESSION['pending_email']);
        $_SESSION['success_message'] = "✅ Your account has been verified successfully!";
        header("Location: index.php");
        exit();
    } else {
        $message = "❌ Invalid or expired code. Please check your email again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <div class="verify-container">
        <h2>Email Verification</h2>
        <p>Enter the 6-digit code sent to <b><?= htmlspecialchars($email) ?></b></p>

        <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>

        <form method="POST">
            <input type="text" name="otp" maxlength="6" placeholder="Enter Code" required>
            <button type="submit" class="login-btn">Verify</button>
        </form>
    </div>

    <style>
        .verify-container {
            width: 400px;
            margin: 80px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        input[name="otp"] {
            text-align: center;
            font-size: 1.5rem;
            letter-spacing: 6px;
        }
    </style>
</body>

</html>