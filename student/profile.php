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

<style>

body{
font-family: Arial;
background:#f4f4f4;
padding:40px;
}

.profile-box{
background:white;
padding:30px;
border-radius:8px;
width:400px;
box-shadow:0 3px 10px rgba(0,0,0,0.1);
}

h2{
margin-bottom:20px;
}

.row{
margin-bottom:12px;
}

.label{
font-weight:bold;
}

.back-btn{
margin-top:20px;
display:inline-block;
padding:8px 12px;
background:#555;
color:white;
text-decoration:none;
}

</style>

</head>

<body>

<div class="profile-box">

<h2>Student Profile</h2>

<div class="row">
<span class="label">Name:</span>
<?php echo $student["name"]; ?>
</div>

<div class="row">
<span class="label">Register Number:</span>
<?php echo $student["register_number"]; ?>
</div>

<div class="row">
<span class="label">Department:</span>
<?php echo $student["department"]; ?>
</div>

<div class="row">
<span class="label">Year:</span>
<?php echo $student["year"]; ?>
</div>

<div class="row">
<span class="label">Room Number:</span>
<?php echo $student["room_number"]; ?>
</div>

<div class="row">
<span class="label">Phone Number:</span>
<?php echo $student["phone"]; ?>
</div>

<div class="row">
<span class="label">Email:</span>
<?php echo $student["email"]; ?>
</div>

<div class="row">
<span class="label">Parent Email:</span>
<?php echo $student["parent_email"]; ?>
</div>

<div class="row">
<span class="label">Teacher Email:</span>
<?php echo $student["teacher_email"]; ?>
</div>

<a class="back-btn" href="dashboard.php">Back to Dashboard</a>

</div>

</body>
</html>