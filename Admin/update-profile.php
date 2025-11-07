<?php
include "../PHP/db_connect.php";
session_start();
date_default_timezone_set('Asia/Manila');

// 🔒 Ensure logged in
if (!isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}

$role = $_SESSION['role'];
$message = "";

// 🧩 Get current user info
if ($role === 'admin') {
    $username = $_SESSION['admin'];
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
} else {
    $librarian_id = $_SESSION['librarian_id'];
    $stmt = $conn->prepare("SELECT * FROM tbl_librarians WHERE librarian_id = ?");
    $stmt->bind_param("i", $librarian_id);
}

$stmt->execute();
$stmt->execute();
$result = $stmt->get_result();
$user = $result ? $result->fetch_assoc() : null;

// 🧱 Prevent null warning
if (!$user) {
    $user = [
        'username' => '',
        'fullname' => '',
        'section' => '',
        'email' => ''
    ];
}


// 🧠 Handle Update Request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($role === 'admin') {
        $new_username = trim($_POST['username']);

        // Update only username for admin
        $update = $conn->prepare("UPDATE admin SET username = ? WHERE username = ?");
        $update->bind_param("ss", $new_username, $username);
    } else {
        $fullname = trim($_POST['fullname']);
        $section = trim($_POST['section']);
        $email = trim($_POST['email']);

        // Update for librarian (no profile pic)
        $update = $conn->prepare("UPDATE tbl_librarians SET fullname = ?, section = ?, email = ? WHERE librarian_id = ?");
        $update->bind_param("sssi", $fullname, $section, $email, $librarian_id);
    }

    if ($update->execute()) {
        if ($role === 'admin') {
            $_SESSION['admin'] = $new_username; // Update session username
        }
        $message = "✅ Profile updated successfully!";
    } else {
        $message = "❌ Error updating profile: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Update Profile | CEIT Thesis Hub</title>
    <link rel="icon" type="image/png" href="pictures/Logo.png" />
    <link rel="stylesheet" href="style.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">


    <style>
        /* 🌐 Base Styling */
        body {
            font-family: "Poppins", sans-serif;
            background: #f4f7fb;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* 🧭 Main Section */
        .settings-page {
            padding: 40px 60px;
            background: #f4f7fb;
            min-height: 100vh;
        }

        .settings-page h2 {
            font-size: 26px;
            color: #0A3D91;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .divider {
            border: none;
            height: 2px;
            background: #e0e0e0;
            margin: 10px 0 25px;
        }

        /* 🧩 Profile Form Card */
        .profile-form {
            background: #fff;
            padding: 40px 50px;
            border-radius: 14px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            max-width: 600px;
            margin: 30px auto;
            transition: 0.3s ease;
        }

        .profile-form:hover {
            transform: translateY(-3px);
        }

        /* 🎯 Form Elements */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #0A3D91;
            margin-bottom: 6px;
            font-size: 15px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border-radius: 8px;
            border: 1px solid #cfd8e3;
            font-size: 15px;
            color: #333;
            outline: none;
            transition: all 0.25s ease;
            background: #f9fbff;
        }

        .form-group input:focus {
            border-color: #0A3D91;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(10, 61, 145, 0.1);
        }

        /* ✅ Success Message */
        p[style*="color:green"] {
            background: #e9f9ef;
            color: #2e7d32 !important;
            border-left: 4px solid #2e7d32;
            padding: 12px 15px;
            border-radius: 8px;
            margin: 10px 0 20px;
            font-weight: 500;
        }

        /* 🔘 Save Button */
        .login-btn {
            display: inline-block;
            width: 100%;
            background: #0A3D91;
            color: #fff;
            padding: 12px 16px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            letter-spacing: 0.3px;
            transition: all 0.25s ease;
            box-shadow: 0 3px 10px rgba(10, 61, 145, 0.2);
        }

        .login-btn:hover {
            background: #083377;
            transform: translateY(-2px);
        }

        /* 📱 Responsive Design */
        @media (max-width: 768px) {
            .settings-page {
                padding: 25px;
            }

            .profile-form {
                padding: 25px;
            }

            .settings-page h2 {
                font-size: 22px;
            }

            .form-group label {
                font-size: 14px;
            }

            .login-btn {
                font-size: 15px;
                padding: 10px 14px;
            }
        }

        /* 🔙 Back Button */
        .back-btn {
            display: inline-block;
            background: #0A3D91;
            color: #fff;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 20px;
            transition: 0.25s ease;
            box-shadow: 0 3px 8px rgba(10, 61, 145, 0.2);
        }

        .back-btn:hover {
            background: #062e72;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(10, 61, 145, 0.3);
        }
    </style>

</head>

<body>
    <header class="main-header">
        <div class="header-left">
            <span class="menu-icon">☰</span>
            <h1>CEIT Thesis Hub</h1>
        </div>
        <div class="header-right">
            <h2>Update Profile</h2>
            <div class="header-logo">
                <img src="pictures/Logo.png" alt="CEIT Logo" width="90" height="60" />
            </div>
        </div>
    </header>

    <div class="container">
        <?php include 'sidebar.php'; ?>
        <main class="settings-page">
            <a href="settings.php" class="back-btn">← Back</a>
            <h2>Profile Settings</h2>
            <hr class="divider" />


            <?php if (!empty($message)) echo "<p style='color:green;'>$message</p>"; ?>

            <form method="POST" class="profile-form">

                <?php if ($role === 'admin'): ?>
                    <div class="form-group">
                        <label>Username:</label>
                        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>
                <?php else: ?>
                    <div class="form-group">
                        <label>Full Name:</label>
                        <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Section:</label>
                        <input type="text" name="section" value="<?= htmlspecialchars($user['section']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                <?php endif; ?>

                <button type="submit" class="login-btn">Save Changes</button>
            </form>
        </main>
    </div>
</body>

</html>