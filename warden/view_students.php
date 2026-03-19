<?php
include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}

/* Fetch students */
$stmt = $conn->prepare("
SELECT 
    users.id,
    users.name,
    users.email,
    student_profiles.register_number,
    student_profiles.department,
    student_profiles.year,
    student_profiles.room_number,
    student_profiles.phone
FROM users
JOIN student_profiles 
ON users.id = student_profiles.user_id
JOIN roles 
ON users.role_id = roles.id
WHERE roles.role_name = 'student'
ORDER BY users.name
");

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>

<title>View Students</title>

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

/* CONTAINER */

.container{
max-width:1200px;
margin:auto;
padding:50px;
background:#fff;
border-radius:22px;

box-shadow:
0 40px 90px rgba(0,0,0,0.12),
0 15px 35px rgba(0,0,0,0.08);
}

h1{
margin-bottom:25px;
}

/* TABLE */

table{
width:100%;
border-collapse:collapse;
background:#fff;
}

th,td{
padding:12px;
border-bottom:1px solid #eee;
text-align:left;
font-size:14px;
}

th{
background:#111;
color:#fff;
font-weight:500;
}

tr:hover{
background:#f7f7f7;
}

/* ACTION BUTTONS */

.action-btn{
padding:6px 12px;
text-decoration:none;
border-radius:6px;
font-size:13px;
margin-right:6px;
}

.edit{
background:#111;
color:white;
}

.delete{
background:#e74c3c;
color:white;
}

.action-btn:hover{
opacity:0.85;
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

<h1>Student List</h1>

<table>

<tr>
<th>Name</th>
<th>Register Number</th>
<th>Department</th>
<th>Year</th>
<th>Room</th>
<th>Phone</th>
<th>Email</th>
<th>Actions</th>
</tr>

<?php while($row = $result->fetch_assoc()) { ?>

<tr>

<td><?php echo $row["name"]; ?></td>
<td><?php echo $row["register_number"]; ?></td>
<td><?php echo $row["department"]; ?></td>
<td><?php echo $row["year"]; ?></td>
<td><?php echo $row["room_number"]; ?></td>
<td><?php echo $row["phone"]; ?></td>
<td><?php echo $row["email"]; ?></td>

<td>

<a class="action-btn edit"
href="edit_student.php?id=<?php echo $row['id']; ?>">
Edit
</a>

<a class="action-btn delete"
href="delete_student.php?id=<?php echo $row['id']; ?>"
onclick="return confirm('Are you sure you want to delete this student?');">
Delete
</a>

</td>

</tr>

<?php } ?>

</table>

<a class="back-btn" href="dashboard.php">← Back to Dashboard</a>

</div>

</body>
</html>