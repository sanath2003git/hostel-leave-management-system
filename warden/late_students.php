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

<style>

body{
font-family:Arial;
background:#f4f4f4;
padding:40px;
}

h2{
margin-bottom:20px;
}

table{
width:100%;
border-collapse:collapse;
background:white;
box-shadow:0 3px 10px rgba(0,0,0,0.08);
}

th,td{
padding:12px;
border-bottom:1px solid #ddd;
text-align:left;
}

th{
background:#c0392b;
color:white;
}

tr:hover{
background:#f2f2f2;
}

.call-btn{
background:#27ae60;
color:white;
padding:5px 10px;
text-decoration:none;
border-radius:4px;
}

.back-btn{
display:inline-block;
margin-top:20px;
background:#555;
color:white;
padding:8px 12px;
text-decoration:none;
}

</style>
</head>

<body>

<h2>Late Students (Not Returned)</h2>

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
<th>Action</th>
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

<td>
<a class="call-btn" href="tel:<?php echo $row['phone']; ?>">
Call Student
</a>
</td>

</tr>

<?php } ?>

</table>

<a class="back-btn" href="dashboard.php">Back to Dashboard</a>

</body>
</html>