<?php
include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}

/* CURRENTLY OUTSIDE STUDENTS (Approved Leave) */
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
WHERE attendance.date = CURDATE()
AND attendance.remark = 'Unauthorized'
AND users.id NOT IN (

    SELECT student_id
    FROM hostel_leaves
    WHERE status='Approved'
    AND NOW() BETWEEN from_datetime AND to_datetime
    AND returned_at IS NULL

)
ORDER BY users.name ASC
");

/* RECENT RETURNS */
$returnedResult = $conn->query("
SELECT hostel_leaves.returned_at,
       hostel_leaves.return_status,
       users.name
FROM hostel_leaves
JOIN users ON hostel_leaves.student_id = users.id
WHERE hostel_leaves.returned_at IS NOT NULL
ORDER BY hostel_leaves.returned_at DESC
LIMIT 10
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
background:linear-gradient(135deg,#e8eaef,#f4f5f8,#e6e8ed);
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
}

.logout-btn{
padding:8px 16px;
background:#222;
color:#fff;
text-decoration:none;
border-radius:8px;
margin-left:12px;
}

.container{
max-width:1400px;
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
margin-bottom:25px;
}

.section{
margin-top:28px;
}

.section h2{
font-size:18px;
margin-bottom:14px;
}

table{
width:100%;
border-collapse:collapse;
}

th{
background:#111;
color:#fff;
padding:14px;
text-align:left;
font-size:14px;
}

td{
padding:14px;
border-bottom:1px solid #eee;
font-size:14px;
}

tr:hover{
background:#f8f9fb;
}

.btn{
padding:8px 12px;
text-decoration:none;
border-radius:8px;
font-size:13px;
color:#fff;
background:#111;
}

.badge{
padding:6px 10px;
border-radius:30px;
font-size:12px;
font-weight:600;
display:inline-block;
}

.red{
background:#fff0f0;
color:#e74c3c;
}

.blue{
background:#eef4ff;
color:#2563eb;
}

.empty{
padding:18px;
background:#fafafa;
border:1px solid #eee;
border-radius:10px;
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
<a href="../auth/logout.php" class="logout-btn">Logout</a>
</div>
</div>

<div class="container">

<h1>Student Outing Monitor</h1>
<div class="sub">Track outside students, unauthorized absences and returns.</div>

<!-- OUTSIDE STUDENTS -->

<div class="section">
<h2>✔ Students Outside (Approved Leave)</h2>

<?php if($leaveResult->num_rows > 0){ ?>

<table>
<tr>
<th>Name</th>
<th>Department</th>
<th>Room</th>
<th>From</th>
<th>To</th>
<th>Action</th>
</tr>

<?php while($row = $leaveResult->fetch_assoc()){ ?>

<tr>
<td><?php echo $row["name"]; ?></td>
<td><?php echo $row["department"]; ?></td>
<td><?php echo $row["room_number"]; ?></td>
<td><?php echo $row["from_datetime"]; ?></td>
<td><?php echo $row["to_datetime"]; ?></td>
<td>
<a class="btn"
href="mark_returned.php?id=<?php echo $row["id"]; ?>"
onclick="return returnConfirm()">Mark Returned</a>
</td>
</tr>

<?php } ?>

</table>

<?php } else { ?>
<div class="empty">No students currently outside.</div>
<?php } ?>

</div>

<!-- UNAUTHORIZED STUDENTS -->

<div class="section">
<h2>🚨 Unauthorized Students</h2>

<?php if($unauthResult->num_rows > 0){ ?>

<table>
<tr>
<th>Name</th>
<th>Department</th>
<th>Room</th>
<th>Date</th>
<th>Status</th>
</tr>

<?php while($row = $unauthResult->fetch_assoc()){ ?>

<tr>
<td><?php echo $row["name"]; ?></td>
<td><?php echo $row["department"]; ?></td>
<td><?php echo $row["room_number"]; ?></td>
<td><?php echo $row["date"]; ?></td>
<td><span class="badge red">Unauthorized</span></td>
</tr>

<?php } ?>

</table>

<?php } else { ?>
<div class="empty">No unauthorized students found today.</div>
<?php } ?>

</div>

<!-- RECENT RETURNS -->

<div class="section">
<h2>↩ Recent Returns</h2>

<?php if($returnedResult->num_rows > 0){ ?>

<table>
<tr>
<th>Name</th>
<th>Returned At</th>
<th>Status</th>
</tr>

<?php while($row = $returnedResult->fetch_assoc()){ ?>

<tr>
<td><?php echo $row["name"]; ?></td>
<td><?php echo $row["returned_at"]; ?></td>
<td><span class="badge blue"><?php echo $row["return_status"]; ?></span></td>
</tr>

<?php } ?>

</table>

<?php } else { ?>
<div class="empty">No returned students yet.</div>
<?php } ?>

</div>

<a href="dashboard.php" class="back-btn">← Back to Dashboard</a>

</div>

</body>
</html>