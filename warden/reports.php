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

/* STUDENTS OUTSIDE */
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

/* STUDENTS INSIDE */
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
WHERE DATE(applied_at)=CURDATE()
")->fetch_assoc()["count"];

/* RETURNED TODAY */
$returned_today = $conn->query("
SELECT COUNT(*) AS count
FROM hostel_leaves
WHERE DATE(returned_at)=CURDATE()
")->fetch_assoc()["count"];

/* RETURNED LATE */
$returned_late = $conn->query("
SELECT COUNT(*) AS count
FROM hostel_leaves
WHERE DATE(returned_at)=CURDATE()
AND return_status='Returned Late'
")->fetch_assoc()["count"];

/* APPROVAL RATE */
$approval_rate = $total_leaves > 0
? round(($approved / $total_leaves) * 100)
: 0;

/* OCCUPANCY */
$occupancy = $total_students > 0
? max(0, round(($in_students / $total_students) * 100))
: 0;

$updated_time = date("h:i A");
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

.container{
max-width:1280px;
margin:auto;
background:#fff;
padding:60px;
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
margin-bottom:30px;
}

.grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
gap:20px;
}

.card{
padding:28px;
border-radius:18px;
background:linear-gradient(135deg,#ffffff,#f8f9fb);
border:1px solid #eee;
box-shadow:0 8px 20px rgba(0,0,0,0.05);
text-align:center;
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

.green{background:#eafaf1;}
.red{background:#fdecea;}
.yellow{background:#fff8e6;}
.blue{background:#eef4ff;}
.lightred{background:#fff0f0;}

.btn{
display:inline-block;
margin-top:28px;
padding:12px 18px;
background:#111;
color:#fff;
text-decoration:none;
border-radius:8px;
}

.btn:hover{
background:#444;
}

.footer{
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
<a href="../auth/logout.php" class="logout-btn">Logout</a>
</div>

</div>

<div class="container">

<h1>System Reports</h1>
<div class="sub">Live analytics dashboard • Auto refresh every 10 seconds</div>

<div class="grid">

<div class="card">
<h3> Total Students</h3>
<p><?php echo $total_students; ?></p>
</div>

<div class="card">
<h3> Leave Applications</h3>
<p><?php echo $total_leaves; ?></p>
</div>

<div class="card green">
<h3> Approved Leaves</h3>
<p><?php echo $approved; ?></p>
</div>

<div class="card blue">
<h3> Pending Leaves</h3>
<p><?php echo $pending; ?></p>
</div>

<div class="card lightred">
<h3> Rejected Leaves</h3>
<p><?php echo $rejected; ?></p>
</div>

<div class="card red">
<h3> Students Outside</h3>
<p><?php echo $out_students; ?></p>
</div>

<div class="card green">
<h3> In Hostel</h3>
<p><?php echo $in_students; ?></p>
</div>

<div class="card lightred">
<h3> Late Students</h3>
<p><?php echo $late_students; ?></p>
</div>

<div class="card yellow">
<h3> Active Mess Cuts</h3>
<p><?php echo $mess_cut_active; ?></p>
</div>

<div class="card">
<h3> Today Requests</h3>
<p><?php echo $today_requests; ?></p>
</div>

<div class="card green">
<h3>Returned Today</h3>
<p><?php echo $returned_today; ?></p>
</div>

<div class="card red">
<h3> Returned Late</h3>
<p><?php echo $returned_late; ?></p>
</div>

<div class="card">
<h3> Approval Rate</h3>
<p><?php echo $approval_rate; ?>%</p>
</div>

<div class="card">
<h3> Occupancy</h3>
<p><?php echo $occupancy; ?>%</p>
</div>

</div>

<a href="dashboard.php" class="btn">← Back to Dashboard</a>

<div class="footer">
Last Updated: <?php echo $updated_time; ?>
</div>

</div>

</body>
</html>