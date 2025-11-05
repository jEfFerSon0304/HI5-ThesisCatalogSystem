<?php
include "../PHP/db_connect.php";

if (isset($_POST['request_id']) && isset($_POST['new_status'])) {
    $id = intval($_POST['request_id']);
    $status = trim($_POST['new_status']);

    // Define valid statuses
    $allowed = ['Pending', 'Approved', 'Rejected', 'Returned', 'Complete'];
    if (!in_array($status, $allowed)) {
        echo "Invalid status.";
        exit();
    }

    // Fetch current status
    $stmt = $conn->prepare("SELECT status FROM tbl_borrow_requests WHERE request_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "Request not found.";
        exit();
    }

    $row = $result->fetch_assoc();
    $current = $row['status'] ?? '';

    // Apply descriptive complete status
    if ($status === 'Complete') {
        if ($current === 'Returned') {
            $status = 'Complete - Returned';
        } elseif ($current === 'Rejected') {
            $status = 'Complete - Rejected';
        }
    }

    // Update request status
    $update_stmt = $conn->prepare("UPDATE tbl_borrow_requests SET status = ? WHERE request_id = ?");
    $update_stmt->bind_param("si", $status, $id);

    if ($update_stmt->execute()) {
        echo "Status updated to $status.";
    } else {
        echo "Error updating status: " . $conn->error;
    }

    $update_stmt->close();
    $stmt->close();
} else {
    echo "Missing POST data.";
}

$conn->close();
