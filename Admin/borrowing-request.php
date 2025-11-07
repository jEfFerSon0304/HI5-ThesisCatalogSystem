<?php
include "../PHP/db_connect.php";
session_start();
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}

$role = $_SESSION['role'];
$displayName = $_SESSION['fullname'] ?? $_SESSION['admin'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Borrowing Requests</title>
<link rel="icon" type="image/png" href="pictures/Logo.png">
<link rel="stylesheet" href="style.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
.container main {
    width: 100%;
    padding: 25px;
}

.request-table table {
    width: 100%;
    border-collapse: collapse;
}

.view-btn {
    background: #007bff;
    color: #fff;
    padding: 7px 14px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
}

.modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.55);
    justify-content: center;
    align-items: center;
    z-index: 2000;
}

.modal-content {
    background: #fff;
    width: 650px;
    padding: 32px;
    border-radius: 12px;
    box-shadow: 0 0 15px rgba(0,0,0,0.25);
    font-family: Poppins, sans-serif;
}

.modal-content .actions button {
    padding: 9px 16px;
    border-radius: 6px;
    margin-right: 8px;
    cursor: pointer;
    border: none;
}

.modal-content .actions button:nth-child(1) { background: #007bff; color: white; }
.modal-content .actions button:nth-child(2) { background: #d9534f; color: white; }
.modal-content .actions button:nth-child(3) { background: #5a5a5a; color: white; }

#notifyModal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.45);
    justify-content: center;
    align-items: center;
    z-index: 3000;
}

#notifyModal .modal-content {
    width: 420px;
    text-align: center;
}

.notify-confirm {
    background: #007bff;
    color: white;
    padding: 9px 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

.notify-cancel {
    background: #d9534f;
    color: white;
    padding: 9px 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
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
        <h2>Admin Dashboard</h2>
        <div class="header-logo">
            <img src="pictures/Logo.png" width="90" height="60">
        </div>
    </div>
</header>

<div class="container">
<?php include 'sidebar.php'; ?>

<main>
<section class="request-header">
    <h2>Borrowing Requests</h2>
</section>

<section class="request-table">
    <table>
        <thead>
            <tr>
                <th>Request #</th>
                <th>Student Name</th>
                <th>Thesis Title</th>
                <th>Department</th>
                <th>Date Requested</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT r.*, t.title, t.author, t.department, t.year 
                FROM tbl_borrow_requests r 
                JOIN tbl_thesis t ON r.thesis_id = t.thesis_id 
                ORDER BY r.request_date DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $json_data = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                echo "
                <tr>
                    <td>{$row['request_number']}</td>
                    <td>{$row['student_name']}</td>
                    <td>{$row['title']}</td>
                    <td>{$row['department']}</td>
                    <td>{$row['request_date']}</td>
                    <td>{$row['status']}</td>
                    <td><button class='view-btn' data-row='{$json_data}' onclick='openModal(this)'>View</button></td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No borrowing requests yet.</td></tr>";
        }
        $conn->close();
        ?>
        </tbody>
    </table>
</section>
</main>
</div>

<div id="viewModal" class="modal">
<div class="modal-content">
    <span class="modal-close" onclick="closeModal()">&times;</span>
    <h3>Request Details</h3>
    <div id="modal-details"></div>
    <div class="actions" id="modal-actions"></div>
</div>
</div>

<div id="notifyModal">
<div class="modal-content">
    <h3 id="notifyTitle"></h3>
    <p id="notifyMessage"></p>
    <div style="margin-top:20px;">
        <button id="notifyConfirm" class="notify-confirm">Confirm</button>
        <button class="notify-cancel" onclick="closeNotifyModal()">Cancel</button>
    </div>
</div>
</div>

<script>
let currentRequest=null,confirmCallback=null;

function openModal(button){
    const data=JSON.parse(button.getAttribute('data-row'));
    currentRequest=data;
    document.getElementById('modal-details').innerHTML=`
        <p><strong>Request #:</strong> ${data.request_number}</p>
        <p><strong>Student:</strong> ${data.student_name}</p>
        <p><strong>Thesis:</strong> ${data.title}</p>
        <p><strong>Date:</strong> ${data.request_date}</p>
        <p><strong>Status:</strong> ${data.status}</p>`;
    let actions="";
    if(data.status==='Pending'){
        actions=`<button onclick="confirmStatus(${data.request_id}, 'Approved')">Approve</button>
                 <button onclick="confirmStatus(${data.request_id}, 'Rejected')">Reject</button>`;
    } else if(data.status==='Approved'){
        actions=`<button onclick="confirmStatus(${data.request_id}, 'Returned')">Mark Returned</button>`;
    } else if(data.status==='Returned'){
        actions=`<button onclick="confirmStatus(${data.request_id}, 'Complete')">Complete</button>`;
    } else {
        actions=`<p style="color:gray;">No actions available.</p>`;
    }
    document.getElementById('modal-actions').innerHTML=actions;
    document.getElementById('viewModal').style.display='flex';
}
function closeModal(){ document.getElementById('viewModal').style.display='none'; }
function confirmStatus(id,status){
    openNotifyModal("Confirm Action","Mark request as <strong>"+status+"</strong>?",()=>updateStatus(id,status));
}
function updateStatus(id,status){
    fetch('update-request-status.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'request_id='+id+'&new_status='+status})
    .then(r=>r.text()).then(msg=>openNotifyModal("Success",msg,()=>location.reload(),true));
}
function openNotifyModal(title,message,onConfirm,hideCancel=false){
    document.getElementById("notifyTitle").innerHTML=title;
    document.getElementById("notifyMessage").innerHTML=message;
    confirmCallback=onConfirm;
    document.getElementById("notifyConfirm").onclick=function(){closeNotifyModal();if(confirmCallback)confirmCallback();};
    document.querySelector(".notify-cancel").style.display=hideCancel?"none":"inline-block";
    document.getElementById("notifyModal").style.display="flex";
}
function closeNotifyModal(){document.getElementById("notifyModal").style.display="none";}
</script>

</body>
</html>
