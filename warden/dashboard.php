<?php
include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}

/* TOTAL REQUESTS */
$total_requests = $conn->query("
SELECT COUNT(*) AS c
FROM hostel_leaves
")->fetch_assoc()["c"];

/* PENDING REQUESTS */
$pending = $conn->query("
SELECT COUNT(*) AS c
FROM hostel_leaves
WHERE status='Pending'
")->fetch_assoc()["c"];

/* STUDENTS OUTSIDE (Approved + Unauthorized Today) */
$out_students = $conn->query("
SELECT COUNT(DISTINCT uid) AS c
FROM (

    SELECT student_id AS uid
    FROM hostel_leaves
    WHERE status='Approved'
    AND returned_at IS NULL

    UNION

    SELECT user_id AS uid
    FROM attendance
    WHERE date = CURDATE()
    AND remark='Unauthorized'

) x
")->fetch_assoc()["c"];

/* LATE STUDENTS */
$late_students = $conn->query("
SELECT COUNT(DISTINCT student_id) AS c
FROM hostel_leaves
WHERE status='Approved'
AND returned_at IS NULL
AND NOW() > to_datetime
")->fetch_assoc()["c"];

/* RETURNED TODAY */
$returned_today = $conn->query("
SELECT COUNT(*) AS c
FROM hostel_leaves
WHERE DATE(returned_at)=CURDATE()
")->fetch_assoc()["c"];
?>

<!DOCTYPE html>
<html>
<head>

<title>Warden Dashboard</title>

<meta http-equiv="refresh" content="10">

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
background:linear-gradient(135deg,#e8eaef,#f4f5f8,#e6e8ed);
padding:120px 60px 60px 60px;
}

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
z-index:1000;
box-shadow:0 6px 20px rgba(0,0,0,0.35);
}

.logo{
font-size:18px;
font-weight:600;
}

.logout-btn{
padding:8px 16px;
background:#222;
color:#fff;
text-decoration:none;
border-radius:8px;
margin-left:12px;
}

.logout-btn:hover{
background:#444;
}

.dashboard{
max-width:1250px;
margin:auto;
background:#fff;
padding:55px;
border-radius:24px;
box-shadow:0 30px 70px rgba(0,0,0,0.10);
}

h1{
font-size:34px;
margin-bottom:8px;
}

.sub{
color:#777;
font-size:14px;
margin-bottom:35px;
}

/* STATS */

.stats{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
gap:18px;
margin-bottom:35px;
}

.stat{
padding:24px;
border-radius:18px;
background:linear-gradient(135deg,#ffffff,#f8f9fb);
border:1px solid #eee;
box-shadow:0 8px 18px rgba(0,0,0,0.05);
}

.stat h3{
font-size:14px;
color:#666;
margin-bottom:8px;
}

.stat p{
font-size:30px;
font-weight:600;
color:#111;
}

/* MENU */

.menu{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
gap:24px;
}

.card{
padding:28px;
border-radius:18px;
background:#fff;
border:1px solid #eee;
box-shadow:0 12px 25px rgba(0,0,0,0.08);
transition:0.25s;
}

.card:hover{
transform:translateY(-6px);
box-shadow:0 20px 40px rgba(0,0,0,0.12);
}

.card h3{
margin-bottom:10px;
font-size:18px;
}

.card p{
font-size:14px;
color:#666;
margin-bottom:18px;
line-height:1.5;
}

.card a{
padding:11px 18px;
background:#111;
color:#fff;
text-decoration:none;
border-radius:10px;
display:inline-block;
font-size:14px;
}

.card a:hover{
background:#444;
}

.footer{
margin-top:30px;
font-size:13px;
color:#777;
}

</style>

</head>

<body>

<div class="topbar">

<div class="logo">Hostel Leave System</div>

<div>
<?php echo $_SESSION["username"]; ?>
<a href="../auth/logout.php" class="logout-btn">Logout</a>
</div>

</div>

<div class="dashboard">

<h1>Warden Dashboard</h1>
<div class="sub">Live hostel control panel with real-time updates.</div>

<!-- STATS -->

<div class="stats">

<div class="stat">
<h3>📝 Total Requests</h3>
<p><?php echo $total_requests; ?></p>
</div>

<div class="stat">
<h3>⏳ Pending Requests</h3>
<p><?php echo $pending; ?></p>
</div>

<div class="stat">
<h3>🔴 Students Outside</h3>
<p><?php echo $out_students; ?></p>
</div>

<div class="stat">
<h3>⚠️ Late Students</h3>
<p><?php echo $late_students; ?></p>
</div>

<!-- <div class="stat">
<h3>↩ Returned Today</h3>
<p><?php echo $returned_today; ?></p>
</div> -->

</div>

<!-- MENU -->

<div class="menu">

<div class="card">
<h3>Leave Requests</h3>
<p>Approve or reject student leave requests.</p>
<a href="view_requests.php">Open</a>
</div>

<div class="card">
<h3>Students Outside</h3>
<p>Monitor current outside and returned students.</p>
<a href="out_students.php">Open</a>
</div>

<div class="card">
<h3>Attendance</h3>
<p>Mark daily attendance for hostel students.</p>
<a href="attendance.php">Open</a>
</div>

<div class="card">
<h3>Reports</h3>
<p>View leave analytics and occupancy reports.</p>
<a href="reports.php">Open</a>
</div>

<div class="card">
<h3>Students</h3>
<p>View, edit and manage student records.</p>
<a href="view_students.php">Open</a>
</div>

<div class="card">
<h3>Add Student</h3>
<p>Register new student accounts.</p>
<a href="add_student.php">Open</a>
</div>

<div class="card">
<h3>Late Students</h3>
<p>Track students who returned after deadline.</p>
<a href="late_students.php">Open</a>
</div>

</div>

<div class="footer">
Auto-refresh every 10 seconds for live hostel monitoring.
</div>

</div>

</body>
</html>