<?php
include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "student") {
    echo "Access Denied";
    exit();
}

$student_id = $_SESSION["user_id"];

/* FETCH LATEST LEAVE */
$stmt = $conn->prepare("
SELECT hostel_leaves.*, leave_types.type_name
FROM hostel_leaves
JOIN leave_types 
    ON hostel_leaves.leave_type_id = leave_types.id
WHERE hostel_leaves.student_id = ?
ORDER BY hostel_leaves.applied_at DESC
LIMIT 1
");

$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>Leave Status</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Poppins',sans-serif;
}

body{
min-height:100vh;
background: linear-gradient(135deg,#dcdde1 0%,#eceef2 40%,#d6d8de 100%);
padding:120px 60px 60px 60px;
}

/* TOPBAR */

.topbar{
position:fixed;
top:0;
left:0;
width:100%;
height:70px;

background:#111;
color:#fff;

display:flex;
justify-content:space-between;
align-items:center;

padding:0 60px;

border-bottom:1px solid #e5e5e5;

z-index:1000;
}

.topbar a{
color:#fff;
text-decoration:none;
font-weight:500;
margin-left:20px;
}

/* CONTAINER */

.dashboard{
max-width:900px;
margin:auto;
padding:60px;
background:#fff;
border-radius:22px;

box-shadow:
0 30px 80px rgba(0,0,0,0.12),
0 10px 25px rgba(0,0,0,0.08);
}

h2{
font-size:28px;
margin-bottom:35px;
}

/* STATUS CARD */

.status-card{
padding:35px;
border-radius:18px;
background:#fff;
border:1px solid #eee;

box-shadow:
0 12px 35px rgba(0,0,0,0.06);
}

.status-row{
margin-bottom:16px;
font-size:15px;
}

.badge{
display:inline-block;
padding:6px 14px;
border-radius:20px;
font-size:13px;
font-weight:600;
}

.pending{
background:#fff3cd;
color:#856404;
}

.approved{
background:#e6f4ea;
color:#1e7e34;
}

.rejected{
background:#fdecea;
color:#c82333;
}

.empty-box{
padding:25px;
background:#fafafa;
border-radius:12px;
border:1px solid #eee;
}

.back-btn{
display:inline-block;
margin-top:20px;
padding:10px 14px;
border:1px solid #111;
border-radius:8px;
text-decoration:none;
color:#fff;
background:#111;
}

.back-btn:hover{
background:#444;
color:#fff;
}

</style>

</head>

<body>

<div class="topbar">

<div>Hostel Leave System</div>

<div>
<?php echo $_SESSION["username"]; ?> |
<a href="../auth/logout.php">Logout</a>
</div>

</div>


<div class="dashboard">

<h2>Latest Leave Status</h2>

<?php if ($result->num_rows == 0): ?>

<div class="empty-box">
No leave applications found.
</div>

<?php else: 
$row = $result->fetch_assoc();
$status = strtolower($row["status"]);
?>

<div class="status-card">

<div class="status-row">
<strong>Leave Type:</strong>
<?php echo htmlspecialchars($row["type_name"]); ?>
</div>

<div class="status-row">
<strong>From:</strong>
<?php echo htmlspecialchars($row["from_datetime"]); ?>
</div>

<div class="status-row">
<strong>To:</strong>
<?php echo htmlspecialchars($row["to_datetime"]); ?>
</div>

<div class="status-row">
<strong>Reason:</strong>
<?php echo htmlspecialchars($row["reason"]); ?>
</div>

<div class="status-row">
<strong>Status:</strong>
<span class="badge <?php echo $status; ?>">
<?php echo htmlspecialchars($row["status"]); ?>
</span>
</div>

</div>

<?php endif; ?>

<a href="dashboard.php" class="back-btn">← Back to Dashboard</a>

</div>

</body>
</html>