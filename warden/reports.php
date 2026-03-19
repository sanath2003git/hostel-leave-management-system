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

/* CURRENTLY OUTSIDE (AUTHORIZED) */
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

/* 🚨 UNAUTHORIZED STUDENTS */
$unauthorized = $conn->query("
    SELECT COUNT(*) AS count
    FROM attendance
    WHERE remark = 'Unauthorized'
")->fetch_assoc()["count"];

/* 📅 TODAY ATTENDANCE */
$today_attendance = $conn->query("
    SELECT COUNT(*) AS count
    FROM attendance
    WHERE date = CURDATE()
")->fetch_assoc()["count"];
?>

<!DOCTYPE html>
<html>
<head>

<title>System Reports</title>

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
background: linear-gradient(135deg,#e8eaef 0%,#f4f5f8 40%,#e6e8ed 100%);
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

display:flex;
justify-content:space-between;
align-items:center;

padding:0 60px;
color:#fff;

box-shadow:0 6px 20px rgba(0,0,0,0.35);
z-index:1000;
}

.logo{
font-size:18px;
font-weight:600;
}

/* LOGOUT BUTTON */

.logout-btn{
padding:8px 16px;
border-radius:8px;
background:#111;
color:#fff; /* ✅ FIXED */
text-decoration:none;
font-size:14px;
margin-left:12px;
}

.logout-btn:hover{
background: rgba(255, 255, 255, 0.15);
}

/* CONTAINER */

.container{
max-width:1100px;
margin:auto;
padding:60px;
background:#fff;
border-radius:22px;

box-shadow:
0 40px 90px rgba(0,0,0,0.12),
0 15px 35px rgba(0,0,0,0.08);
}

h1{
margin-bottom:35px;
}

/* GRID */

.report-container{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
gap:20px;
}

/* CARD */

.card{
background:#fafafa;
padding:28px;
border-radius:16px;
border:1px solid #eee;
text-align:center;

box-shadow:0 8px 20px rgba(0,0,0,0.05);
transition:0.2s;
}

.card:hover{
transform:translateY(-4px);
}

.card h3{
font-size:14px;
color:#666;
margin-bottom:8px;
}

.card p{
font-size:30px;
font-weight:600;
color:#111;
}

/* BACK BUTTON */

.back-btn{
display:inline-block;
margin-top:20px;
padding:10px 14px;
border-radius:8px;
text-decoration:none;
color:#fff;
background:#111;
}

.back-btn:hover{
background:#444;
}

</style>

</head>

<body>

<div class="topbar">

<div class="logo">Hostel Leave System</div>

<div>
<?php echo $_SESSION["username"]; ?>
<a class="logout-btn" href="../auth/logout.php">Logout</a>
</div>

</div>

<div class="container">

<h1>System Reports</h1>

<div class="report-container">

<div class="card">
<h3>Total Students</h3>
<p><?php echo $total_students; ?></p>
</div>

<div class="card">
<h3>Total Leave Applications</h3>
<p><?php echo $total_leaves; ?></p>
</div>

<div class="card">
<h3>Approved Leaves</h3>
<p><?php echo $approved; ?></p>
</div>

<div class="card">
<h3>Pending Leaves</h3>
<p><?php echo $pending; ?></p>
</div>

<div class="card">
<h3>Rejected Leaves</h3>
<p><?php echo $rejected; ?></p>
</div>

<div class="card">
<h3>Students Outside (Authorized)</h3>
<p><?php echo $out_students; ?></p>
</div>

<div class="card">
<h3>Late Students</h3>
<p><?php echo $late_students; ?></p>
</div>

<div class="card">
<h3>🚨 Unauthorized Students</h3>
<p style="color:red;"><?php echo $unauthorized; ?></p>
</div>

<div class="card">
<h3>📅 Today's Attendance</h3>
<p><?php echo $today_attendance; ?></p>
</div>

</div>

<a class="back-btn" href="dashboard.php">← Back to Dashboard</a>

</div>

</body>
</html>