<?php

include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}

/* ✅ AUTHORIZED STUDENTS (WITH LEAVE) */
$leaveStmt = $conn->prepare("

    SELECT users.name,
           student_profiles.department,
           student_profiles.room_number,
           hostel_leaves.from_datetime,
           hostel_leaves.to_datetime

    FROM hostel_leaves

    JOIN users ON hostel_leaves.student_id = users.id
    JOIN student_profiles ON users.id = student_profiles.user_id

    WHERE hostel_leaves.status = 'Approved'
      AND hostel_leaves.returned_at IS NULL

    ORDER BY hostel_leaves.from_datetime ASC

");

$leaveStmt->execute();
$leaveResult = $leaveStmt->get_result();


/* 🚨 UNAUTHORIZED STUDENTS (NO LEAVE) */
$unauthStmt = $conn->prepare("

    SELECT users.name,
           student_profiles.department,
           student_profiles.room_number,
           attendance.date,
           attendance.status

    FROM attendance

    JOIN users ON attendance.user_id = users.id
    JOIN student_profiles ON users.id = student_profiles.user_id

    WHERE attendance.remark = 'Unauthorized'

    ORDER BY attendance.date DESC

");

$unauthStmt->execute();
$unauthResult = $unauthStmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>

<title>Student Outing Monitor</title>

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

.logout-btn{
padding:8px 16px;
border-radius:8px;
background:#111;
color:#fff;
text-decoration:none;
font-size:14px;
}

.logout-btn:hover{
background: rgba(255,255,255,0.15);
}

.container{
max-width:1100px;
margin:auto;
padding:60px;
background:#fff;
border-radius:22px;
box-shadow:0 40px 90px rgba(0,0,0,0.12);
}

h1{
margin-bottom:20px;
}

.section-title{
margin:30px 0 10px;
font-size:18px;
}

table{
width:100%;
border-collapse:collapse;
}

th,td{
padding:14px;
border-bottom:1px solid #eee;
text-align:left;
}

th{
background:#111;
color:#fff;
}

tr:hover{
background:#f7f7f7;
}

.empty{
padding:15px;
background:#fafafa;
border:1px solid #eee;
border-radius:10px;
}

.back-btn{
display:inline-block;
margin-top:20px;
padding:10px 14px;
background:#111;
color:#fff;
text-decoration:none;
border-radius:8px;
}

.back-btn:hover{
background:#444;
}

</style>

</head>

<body>

<div class="topbar">
<div>Hostel Leave System</div>
<div>
<?php echo $_SESSION["username"]; ?>
<a class="logout-btn" href="../auth/logout.php">Logout</a>
</div>
</div>

<div class="container">

<h1>Student Outing Monitor</h1>

<!-- ✅ AUTHORIZED -->
<div class="section-title">✔ Students Outside (With Permission)</div>

<?php if ($leaveResult->num_rows == 0): ?>
<div class="empty">No authorized students outside.</div>
<?php else: ?>

<table>
<tr>
<th>Name</th>
<th>Department</th>
<th>Room</th>
<th>From</th>
<th>To</th>
</tr>

<?php while ($row = $leaveResult->fetch_assoc()): ?>
<tr>
<td><?php echo htmlspecialchars($row["name"]); ?></td>
<td><?php echo htmlspecialchars($row["department"]); ?></td>
<td><?php echo htmlspecialchars($row["room_number"]); ?></td>
<td><?php echo htmlspecialchars($row["from_datetime"]); ?></td>
<td><?php echo htmlspecialchars($row["to_datetime"]); ?></td>
</tr>
<?php endwhile; ?>

</table>

<?php endif; ?>


<!-- 🚨 UNAUTHORIZED -->
<div class="section-title">🚨 Unauthorized Students</div>

<?php if ($unauthResult->num_rows == 0): ?>
<div class="empty">No unauthorized students found.</div>
<?php else: ?>

<table>
<tr>
<th>Name</th>
<th>Department</th>
<th>Room</th>
<th>Date</th>
<th>Status</th>
</tr>

<?php while ($row = $unauthResult->fetch_assoc()): ?>
<tr>
<td><?php echo htmlspecialchars($row["name"]); ?></td>
<td><?php echo htmlspecialchars($row["department"]); ?></td>
<td><?php echo htmlspecialchars($row["room_number"]); ?></td>
<td><?php echo htmlspecialchars($row["date"]); ?></td>
<td style="color:red;">
<?php echo htmlspecialchars($row["status"]); ?> (Unauthorized)
</td>
</tr>
<?php endwhile; ?>

</table>

<?php endif; ?>

<a class="back-btn" href="dashboard.php">← Back to Dashboard</a>

</div>

</body>
</html>