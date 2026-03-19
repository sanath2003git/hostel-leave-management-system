<?php
include("../includes/auth_check.php");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Warden Dashboard</title>

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
border-bottom:1px solid #222;

z-index:1000;
}

.logo{
font-size:18px;
font-weight:600;
letter-spacing:0.5px;
}

.topbar a{
text-decoration:none;
color:#fff;
font-weight:500;
}

/* LOGOUT BUTTON */

.logout-btn{
padding:8px 16px;
border-radius:8px;
background:#111;
color:1#fff;
text-decoration:none;
font-size:14px;
font-weight:500;
margin-left:12px;
transition:0.2s;
}

.logout-btn:hover{
background: rgba(255, 255, 238, 0.15);
}

/* MAIN DASHBOARD */

.dashboard{
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
font-size:32px;
margin-bottom:10px;
}

.subtitle{
color:#777;
margin-bottom:45px;
font-size:15px;
}

/* ACTION GRID */

.actions{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
gap:30px;
margin-top:20px;
}

.action-card{
padding:30px;
border-radius:18px;
background:#fff;
color:#111;

transition:all 0.25s ease;

box-shadow:0 12px 25px rgba(0,0,0,0.25);
}

.action-card:hover{
transform:translateY(-6px);
box-shadow:0 20px 40px rgba(0,0,0,0.35);
}

.action-card h3{
margin-bottom:10px;
}

.action-card p{
font-size:14px;
color:#111;
margin-bottom:18px;
}

/* BUTTON */

.action-card a{
padding:11px 20px;
background:#111;
color:#fff;
text-decoration:none;
border-radius:10px;
font-size:14px;
font-weight:500;
transition:all 0.2s ease;
}

.action-card a:hover{
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


<div class="dashboard">

<h1>Warden Dashboard</h1>

<div class="subtitle">
Manage hostel leave requests and student records.
</div>


<div class="actions">

<div class="action-card">
<h3>Leave Requests</h3>
<p>Review and approve student leave applications.</p>
<a href="view_requests.php">View Requests</a>
</div>

<div class="action-card">
<h3>Students Outside</h3>
<p>Monitor students currently outside the hostel.</p>
<a href="out_students.php">View Students</a>
</div>

<div class="action-card">
<h3>Reports</h3>
<p>Generate leave statistics and reports.</p>
<a href="reports.php">View Reports</a>
</div>

<div class="action-card">
<h3>Add Student</h3>
<p>Register a new student into the hostel system.</p>
<a href="add_student.php">Add Student</a>
</div>

<div class="action-card">
<h3>View Students</h3>
<p>Browse and manage registered students.</p>
<a href="view_students.php">View Students</a>
</div>

<div class="action-card">
<h3>Late Students</h3>
<p>Track students who returned late from leave.</p>
<a href="late_students.php">View Late Students</a>
</div>

<div class="action-card">
<h3>Attendance</h3>
<p> Mark students Attendance.</p>
<a href="attendance.php">Mark Attendance</a>
</div>

</div >
</div>

</body>
</html>