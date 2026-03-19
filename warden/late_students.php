<?php
include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}

/* Fetch Late Students */

$stmt = $conn->prepare("
SELECT 
    users.name,
    users.email,
    student_profiles.register_number,
    student_profiles.department,
    student_profiles.room_number,
    student_profiles.phone,
    hostel_leaves.from_datetime,
    hostel_leaves.to_datetime
FROM hostel_leaves
JOIN users 
    ON hostel_leaves.student_id = users.id
JOIN student_profiles 
    ON users.id = student_profiles.user_id
WHERE hostel_leaves.status = 'Approved'
AND hostel_leaves.returned_at IS NULL
AND NOW() > hostel_leaves.to_datetime
ORDER BY hostel_leaves.to_datetime ASC
");

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>

<title>Late Students</title>

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

/* PAGE CONTAINER */

.container{
max-width:1200px;
margin:auto;
padding:50px;
background:#fff;
border-radius:20px;

box-shadow:
0 40px 90px rgba(0,0,0,0.12),
0 15px 35px rgba(0,0,0,0.08);
}

h1{
margin-bottom:30px;
}

/* TABLE */

table{
width:100%;
border-collapse:collapse;
border-radius:10px;
overflow:hidden;
}

th{
background:#111;
color:white;
text-align:left;
padding:14px;
font-size:14px;
}

td{
padding:14px;
border-bottom:1px solid #eee;
font-size:14px;
color:#555;
}

tr:hover{
background:#f7f7f7;
}

/* LATE ROW HIGHLIGHT */

tr{
border-left:4px solid #e74c3c;
}

/* CALL BUTTON */

.call-btn{
background:#111;
color:white;
padding:7px 12px;
text-decoration:none;
border-radius:8px;
font-size:13px;
transition:0.2s;
}

.call-btn:hover{
background:#000;
}

/* EMPTY STATE */

.empty-box{
padding:25px;
background:#fafafa;
border-radius:12px;
border:1px solid #eee;
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

<div class="topbar">

<div class="logo">Hostel Leave System</div>

<div>
<?php echo $_SESSION["username"]; ?>
<a class="logout-btn" href="../auth/logout.php">Logout</a>
</div>

</div>

<div class="container">

<h1>Late Students (Not Returned)</h1>

<?php if ($result->num_rows == 0): ?>

<div class="empty-box">
No late students currently.
</div>

<?php else: ?>

<table>

<tr>
<th>Name</th>
<th>Register No</th>
<th>Department</th>
<th>Room</th>
<th>Phone</th>
<th>Email</th>
<th>Leave From</th>
<th>Leave To</th>
</tr>

<?php while($row = $result->fetch_assoc()) { ?>

<tr>

<td><?php echo $row["name"]; ?></td>
<td><?php echo $row["register_number"]; ?></td>
<td><?php echo $row["department"]; ?></td>
<td><?php echo $row["room_number"]; ?></td>
<td><?php echo $row["phone"]; ?></td>
<td><?php echo $row["email"]; ?></td>
<td><?php echo $row["from_datetime"]; ?></td>
<td><?php echo $row["to_datetime"]; ?></td>
</tr>

<?php } ?>

</table>

<?php endif; ?>

<a class="back-btn" href="dashboard.php">← Back to Dashboard</a>

</div>

</body>
</html>