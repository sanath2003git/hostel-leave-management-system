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
WHERE roles.role_name='student'
")->fetch_assoc()["count"];

/* TOTAL LEAVE APPLICATIONS */
$total_leaves = $conn->query("
SELECT COUNT(*) AS count
FROM hostel_leaves
")->fetch_assoc()["count"];

/* APPROVED */
$approved = $conn->query("
SELECT COUNT(*) AS count
FROM hostel_leaves
WHERE status='Approved'
")->fetch_assoc()["count"];

/* PENDING */
$pending = $conn->query("
SELECT COUNT(*) AS count
FROM hostel_leaves
WHERE status='Pending'
")->fetch_assoc()["count"];

/* REJECTED */
$rejected = $conn->query("
SELECT COUNT(*) AS count
FROM hostel_leaves
WHERE status='Rejected'
")->fetch_assoc()["count"];

/* STUDENTS OUTSIDE (Approved Leave + Unauthorized Absent Today) */
$out_students = $conn->query("
SELECT COUNT(DISTINCT uid) AS count
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
")->fetch_assoc()["count"];

/* STUDENTS INSIDE (SAFE) */
$in_students = max(0, $total_students - $out_students);

/* LATE STUDENTS */
$late_students = $conn->query("
SELECT COUNT(DISTINCT student_id) AS count
FROM hostel_leaves
WHERE status='Approved'
AND returned_at IS NULL
AND NOW() > to_datetime
")->fetch_assoc()["count"];

/* ACTIVE MESS CUT */
$mess_cut_active = $conn->query("
SELECT COUNT(DISTINCT student_id) AS count
FROM hostel_leaves
WHERE status='Approved'
AND returned_at IS NULL
AND mess_cut = 1
")->fetch_assoc()["count"];

/* TODAY REQUESTS */
$today_requests = $conn->query("
SELECT COUNT(*) AS count
FROM hostel_leaves
WHERE DATE(applied_at) = CURDATE()
")->fetch_assoc()["count"];

/* APPROVAL RATE (SAFE) */
$approval_rate = $total_leaves > 0
? round(($approved / $total_leaves) * 100)
: 0;

/* OCCUPANCY (SAFE) */
$occupancy = $total_students > 0
? max(0, round(($in_students / $total_students) * 100))
: 0;
?>

<!DOCTYPE html>
<html>
<head>

<title>System Reports</title>

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
background:linear-gradient(135deg,#e8eaef 0%,#f4f5f8 40%,#e6e8ed 100%);
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
box-shadow:0 6px 20px rgba(0,0,0,0.35);
z-index:1000;
}

.logo{
font-size:18px;
font-weight:600;
}

.logout-btn{
padding:8px 16px;
border-radius:8px;
background:#222;
color:#fff;
text-decoration:none;
font-size:14px;
margin-left:12px;
}

.logout-btn:hover{
background:#444;
}

.container{
max-width:1250px;
margin:auto;
padding:60px;
background:#fff;
border-radius:24px;
box-shadow:
0 40px 90px rgba(0,0,0,0.12),
0 15px 35px rgba(0,0,0,0.08);
}

h1{
font-size:34px;
margin-bottom:10px;
}

.subtext{
color:#777;
font-size:14px;
margin-bottom:35px;
}

.report-container{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
gap:20px;
}

.card{
background:linear-gradient(135deg,#ffffff,#f8f9fb);
padding:28px;
border-radius:18px;
border:1px solid #eee;
text-align:center;
box-shadow:0 8px 20px rgba(0,0,0,0.05);
transition:0.25s;
}

.card:hover{
transform:translateY(-5px);
box-shadow:0 15px 30px rgba(0,0,0,0.10);
}

.card h3{
font-size:14px;
color:#666;
margin-bottom:8px;
}

.card p{
font-size:32px;
font-weight:600;
color:#111;
}

.in-card{background:#eafaf1;}
.out-card{background:#fdecea;}
.late-card{background:#fff1f1;}
.mess-card{background:#fff8e6;}
.pending-card{background:#eef4ff;}
.reject-card{background:#fff0f0;}

.back-btn{
display:inline-block;
margin-top:30px;
padding:12px 18px;
border-radius:8px;
text-decoration:none;
color:#fff;
background:#111;
}

.back-btn:hover{
background:#444;
}

.footer-note{
margin-top:20px;
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
<a class="logout-btn" href="../auth/logout.php">Logout</a>
</div>

</div>

<div class="container">

<h1>System Reports</h1>
<div class="subtext">Live dashboard refreshes every 10 seconds</div>

<div class="report-container">

<div class="card">
<h3>👨‍🎓 Total Students</h3>
<p><?php echo $total_students; ?></p>
</div>

<div class="card">
<h3>📝 Leave Applications</h3>
<p><?php echo $total_leaves; ?></p>
</div>

<div class="card">
<h3>✅ Approved Leaves</h3>
<p><?php echo $approved; ?></p>
</div>

<div class="card pending-card">
<h3>⏳ Pending Leaves</h3>
<p><?php echo $pending; ?></p>
</div>

<div class="card reject-card">
<h3>❌ Rejected Leaves</h3>
<p><?php echo $rejected; ?></p>
</div>

<div class="card out-card">
<h3>🔴 Students Outside</h3>
<p><?php echo $out_students; ?></p>
</div>

<div class="card in-card">
<h3>🟢 In Hostel</h3>
<p><?php echo $in_students; ?></p>
</div>

<div class="card late-card">
<h3>⚠️ Late Students</h3>
<p><?php echo $late_students; ?></p>
</div>

<div class="card mess-card">
<h3>🍽️ Active Mess Cuts</h3>
<p><?php echo $mess_cut_active; ?></p>
</div>

<div class="card">
<h3>📅 Today Requests</h3>
<p><?php echo $today_requests; ?></p>
</div>

<div class="card">
<h3>📈 Approval Rate</h3>
<p><?php echo $approval_rate; ?>%</p>
</div>

<div class="card">
<h3>🏨 Occupancy</h3>
<p><?php echo $occupancy; ?>%</p>
</div>

</div>

<a class="back-btn" href="dashboard.php">← Back to Dashboard</a>

<div class="footer-note">
Auto-updating hostel analytics dashboard.
</div>

</div>

</body>
</html>