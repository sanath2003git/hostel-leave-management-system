<?php
include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}

/* TOTAL STUDENTS */
$total_students = $conn->query("
    SELECT COUNT(*) AS count 
    FROM users 
    JOIN roles ON users.role_id = roles.id
    WHERE roles.role_name = 'student'
")->fetch_assoc()["count"];

/* TOTAL LEAVES */
$total_leaves = $conn->query("
    SELECT COUNT(*) AS count 
    FROM hostel_leaves
")->fetch_assoc()["count"];

/* APPROVED */
$approved = $conn->query("
    SELECT COUNT(*) AS count 
    FROM hostel_leaves
    WHERE status = 'Approved'
")->fetch_assoc()["count"];

/* PENDING */
$pending = $conn->query("
    SELECT COUNT(*) AS count 
    FROM hostel_leaves
    WHERE status = 'Pending'
")->fetch_assoc()["count"];

/* REJECTED */
$rejected = $conn->query("
    SELECT COUNT(*) AS count 
    FROM hostel_leaves
    WHERE status = 'Rejected'
")->fetch_assoc()["count"];

/* CURRENTLY OUTSIDE */
$out_students = $conn->query("
    SELECT COUNT(*) AS count 
    FROM hostel_leaves
    WHERE status = 'Approved'
    AND returned_at IS NULL
")->fetch_assoc()["count"];

/* LATE STUDENTS */
$late_students = $conn->query("
    SELECT COUNT(*) AS count
    FROM hostel_leaves
    WHERE status = 'Approved'
    AND returned_at IS NULL
    AND NOW() > to_datetime
")->fetch_assoc()["count"];
?>

<!DOCTYPE html>
<html>
<head>
<title>System Reports</title>

<style>

body{
    font-family: Arial, sans-serif;
    padding:40px;
    background:#f4f4f4;
}

h2{
    margin-bottom:30px;
}

.report-container{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:20px;
}

.card{
    background:white;
    padding:25px;
    border-radius:8px;
    text-align:center;
    box-shadow:0 3px 10px rgba(0,0,0,0.08);
    transition:0.2s;
}

.card:hover{
    transform:translateY(-5px);
}

.card h3{
    margin-bottom:10px;
    font-size:16px;
    color:#555;
}

.card p{
    font-size:28px;
    font-weight:bold;
}

.approved{ color:green; }
.pending{ color:orange; }
.rejected{ color:red; }
.late{ color:#c0392b; }

.back-btn{
    display:inline-block;
    margin-top:30px;
    background:#555;
    color:white;
    padding:8px 12px;
    text-decoration:none;
}

</style>
</head>

<body>

<h2>System Reports</h2>

<div class="report-container">

<div class="card">
<h3>Total Students</h3>
<p><?php echo $total_students; ?></p>
</div>

<div class="card">
<h3>Total Leave Applications</h3>
<p><?php echo $total_leaves; ?></p>
</div>

<div class="card approved">
<h3>Approved Leaves</h3>
<p><?php echo $approved; ?></p>
</div>

<div class="card pending">
<h3>Pending Leaves</h3>
<p><?php echo $pending; ?></p>
</div>

<div class="card rejected">
<h3>Rejected Leaves</h3>
<p><?php echo $rejected; ?></p>
</div>

<div class="card">
<h3>Students Currently Outside</h3>
<p><?php echo $out_students; ?></p>
</div>

<div class="card late">
<h3>Late Students</h3>
<p><?php echo $late_students; ?></p>
</div>

</div>

<a class="back-btn" href="dashboard.php">Back to Dashboard</a>

</body>
</html>