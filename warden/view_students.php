<?php
include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}

$search = isset($_GET["search"]) ? trim($_GET["search"]) : "";

if ($search != "") {

    $stmt = $conn->prepare("
    SELECT users.id, users.name, users.email,
           student_profiles.register_number,
           student_profiles.department,
           student_profiles.year,
           student_profiles.room_number,
           student_profiles.phone
    FROM users
    LEFT JOIN student_profiles ON users.id = student_profiles.user_id
    JOIN roles ON users.role_id = roles.id
    WHERE roles.role_name='student'
    AND (
        users.name LIKE ?
        OR student_profiles.register_number LIKE ?
        OR student_profiles.department LIKE ?
    )
    ORDER BY users.name ASC
    ");

    $like = "%$search%";
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();

} else {

    $result = $conn->query("
    SELECT users.id, users.name, users.email,
           student_profiles.register_number,
           student_profiles.department,
           student_profiles.year,
           student_profiles.room_number,
           student_profiles.phone
    FROM users
    LEFT JOIN student_profiles ON users.id = student_profiles.user_id
    JOIN roles ON users.role_id = roles.id
    WHERE roles.role_name='student'
    ORDER BY users.name ASC
    ");
}
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

.search-box{
display:flex;
gap:10px;
margin-bottom:25px;
}

.search-box input{
padding:12px;
width:320px;
border:1px solid #ddd;
border-radius:10px;
outline:none;
}

.search-box button{
padding:12px 18px;
background:#111;
color:#fff;
border:none;
border-radius:10px;
cursor:pointer;
}

.search-box button:hover{
background:#444;
}

table{
width:100%;
border-collapse:collapse;
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

.btn{
padding:8px 12px;
text-decoration:none;
border-radius:8px;
font-size:13px;
display:inline-block;
margin-right:6px;
}

.edit{
background:#111;
color:#fff;
}

.delete{
background:#e74c3c;
color:#fff;
}

.btn:hover{
opacity:0.85;
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
function deleteConfirm(){
return confirm("Are you sure you want to delete this student?");
}
</script>

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
<div class="sub">View and manage registered hostel students.</div>

<form method="GET" class="search-box">
<input type="text" name="search" placeholder="Search name / register no / department" value="<?php echo htmlspecialchars($search); ?>">
<button type="submit">Search</button>
</form>

<?php if($result->num_rows > 0) { ?>

<table>

<tr>
<th>Name</th>
<th>Register No</th>
<th>Department</th>
<th>Year</th>
<th>Room</th>
<th>Phone</th>
<th>Email</th>
<th>Actions</th>
</tr>

<?php while($row = $result->fetch_assoc()) { ?>

<tr>

<td><?php echo htmlspecialchars($row["name"]); ?></td>
<td><?php echo htmlspecialchars($row["register_number"]); ?></td>
<td><?php echo htmlspecialchars($row["department"]); ?></td>
<td><?php echo htmlspecialchars($row["year"]); ?></td>
<td><?php echo htmlspecialchars($row["room_number"]); ?></td>
<td><?php echo htmlspecialchars($row["phone"]); ?></td>
<td><?php echo htmlspecialchars($row["email"]); ?></td>

<td>

<a class="btn edit"
href="edit_student.php?id=<?php echo $row['id']; ?>">
Edit
</a>

<a class="btn delete"
href="delete_student.php?id=<?php echo $row['id']; ?>"
onclick="return deleteConfirm()">
Delete
</a>

</td>

</tr>

<?php } ?>

</table>

<?php } else { ?>

<div class="empty">No students found.</div>

<?php } ?>

<a href="dashboard.php" class="back-btn">← Back to Dashboard</a>

</div>

</body>
</html>