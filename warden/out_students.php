<?php
include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}

/* AUTHORIZED STUDENTS OUTSIDE */
$leaveResult = $conn->query("
SELECT hostel_leaves.id,
       hostel_leaves.student_id,
       hostel_leaves.from_datetime,
       hostel_leaves.to_datetime,
       users.name,
       student_profiles.department,
       student_profiles.room_number
FROM hostel_leaves
JOIN users ON hostel_leaves.student_id = users.id
LEFT JOIN student_profiles ON users.id = student_profiles.user_id
WHERE hostel_leaves.status='Approved'
AND hostel_leaves.returned_at IS NULL
ORDER BY hostel_leaves.from_datetime DESC
");

/* UNAUTHORIZED STUDENTS */
$unauthResult = $conn->query("
SELECT attendance.date,
       attendance.status,
       users.name,
       student_profiles.department,
       student_profiles.room_number
FROM attendance
JOIN users ON attendance.user_id = users.id
LEFT JOIN student_profiles ON users.id = student_profiles.user_id
WHERE attendance.remark='Unauthorized'
ORDER BY attendance.date DESC
");
?>

<!DOCTYPE html>
<html>
<head>

<title>Students Outside</title>

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
padding:120px 50px 50px 50px;
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
padding:0 50px;
color:#fff;
z-index:1000;
box-shadow:0 6px 20px rgba(0,0,0,0.35);
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
max-width:1350px;
margin:auto;
background:#fff;
padding:50px;
border-radius:24px;
box-shadow:0 30px 70px rgba(0,0,0,0.10);
}

h1{
font-size:32px;
margin-bottom:10px;
}

.sub{
color:#777;
font-size:14px;
margin-bottom:30px;
}

.section-title{
margin:28px 0 14px;
font-size:18px;
font-weight:600;
}

table{
width:100%;
border-collapse:collapse;
margin-bottom:10px;
}

th{
background:#111;
color:#fff;
padding:14px;
font-size:14px;
text-align:left;
}

td{
padding:14px;
border-bottom:1px solid #eee;
font-size:14px;
}

tr:hover{
background:#f8f9fb;
}

.empty{
padding:18px;
background:#fafafa;
border:1px solid #eee;
border-radius:10px;
margin-bottom:15px;
}

.btn{
padding:8px 12px;
text-decoration:none;
border-radius:8px;
font-size:13px;
display:inline-block;
}

.return-btn{
background:#111;
color:#fff;
}

.return-btn:hover{
background:#444;
}

.red{
color:#e74c3c;
font-weight:600;
}

.back-btn{
display:inline-block;
margin-top:25px;
padding:12px 18px;
background:#111;
color:#fff;
text-decoration:none;
border-radius:8px;
}

.back-btn:hover{
background:#444;
}

</style>

<script>
function returnConfirm(){
return confirm("Mark student as returned?");
}
</script>

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
<div class="sub">Track authorized outside students and unauthorized absentees.</div>

<!-- AUTHORIZED -->

<div class="section-title">✔ Students Outside (With Permission)</div>

<?php if($leaveResult->num_rows > 0) { ?>

<table>

<tr>
<th>Name</th>
<th>Department</th>
<th>Room</th>
<th>From</th>
<th>To</th>
<th>Action</th>
</tr>

<?php while($row = $leaveResult->fetch_assoc()) { ?>

<tr>

<td><?php echo htmlspecialchars($row["name"]); ?></td>
<td><?php echo htmlspecialchars($row["department"]); ?></td>
<td><?php echo htmlspecialchars($row["room_number"]); ?></td>
<td><?php echo htmlspecialchars($row["from_datetime"]); ?></td>
<td><?php echo htmlspecialchars($row["to_datetime"]); ?></td>

<td>
<a class="btn return-btn"
href="mark_returned.php?id=<?php echo $row['id']; ?>"
onclick="return returnConfirm()">Mark Returned</a>
</td>

</tr>

<?php } ?>

</table>

<?php } else { ?>

<div class="empty">No authorized students outside.</div>

<?php } ?>

<!-- UNAUTHORIZED -->

<div class="section-title">🚨 Unauthorized Students</div>

<?php if($unauthResult->num_rows > 0) { ?>

<table>

<tr>
<th>Name</th>
<th>Department</th>
<th>Room</th>
<th>Date</th>
<th>Status</th>
</tr>

<?php while($row = $unauthResult->fetch_assoc()) { ?>

<tr>

<td><?php echo htmlspecialchars($row["name"]); ?></td>
<td><?php echo htmlspecialchars($row["department"]); ?></td>
<td><?php echo htmlspecialchars($row["room_number"]); ?></td>
<td><?php echo htmlspecialchars($row["date"]); ?></td>
<td class="red"><?php echo htmlspecialchars($row["status"]); ?> (Unauthorized)</td>

</tr>

<?php } ?>

</table>

<?php } else { ?>

<div class="empty">No unauthorized students found.</div>

<?php } ?>

<a href="dashboard.php" class="back-btn">← Back to Dashboard</a>

</div>

</body>
</html>