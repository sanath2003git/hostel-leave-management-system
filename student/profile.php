<?php
include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "student") {
    echo "Access Denied";
    exit();
}

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare("
SELECT 
    users.name,
    users.email,
    users.parent_email,
    users.teacher_email,
    student_profiles.register_number,
    student_profiles.department,
    student_profiles.year,
    student_profiles.room_number,
    student_profiles.phone
FROM users
JOIN student_profiles 
ON users.id = student_profiles.user_id
WHERE users.id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Profile</title>

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
max-width:700px;
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

/* PROFILE ROW */

.row{
margin-bottom:16px;
font-size:15px;
color:#444;
}

.label{
font-weight:600;
margin-right:8px;
}

/* BACK BUTTON */

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

<!-- TOPBAR -->

<div class="topbar">

<div>Hostel Leave System</div>

<div>
<?php echo $_SESSION["username"]; ?> |
<a href="../auth/logout.php">Logout</a>
</div>

</div>


<div class="dashboard">

<h2>Student Profile</h2>

<div class="row">
<span class="label">Name:</span>
<?php echo htmlspecialchars($student["name"]); ?>
</div>

<div class="row">
<span class="label">Register Number:</span>
<?php echo htmlspecialchars($student["register_number"]); ?>
</div>

<div class="row">
<span class="label">Department:</span>
<?php echo htmlspecialchars($student["department"]); ?>
</div>

<div class="row">
<span class="label">Year:</span>
<?php echo htmlspecialchars($student["year"]); ?>
</div>

<div class="row">
<span class="label">Room Number:</span>
<?php echo htmlspecialchars($student["room_number"]); ?>
</div>

<div class="row">
<span class="label">Phone Number:</span>
<?php echo htmlspecialchars($student["phone"]); ?>
</div>

<div class="row">
<span class="label">Email:</span>
<?php echo htmlspecialchars($student["email"]); ?>
</div>

<div class="row">
<span class="label">Parent Email:</span>
<?php echo htmlspecialchars($student["parent_email"]); ?>
</div>

<div class="row">
<span class="label">Teacher Email:</span>
<?php echo htmlspecialchars($student["teacher_email"]); ?>
</div>

<a class="back-btn" href="dashboard.php">← Back to Dashboard</a>

</div>

</body>
</html>