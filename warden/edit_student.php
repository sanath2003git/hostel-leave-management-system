<?php
include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}

if (!isset($_GET["id"])) {
    echo "Invalid Request";
    exit();
}

$user_id = intval($_GET["id"]);

// Fetch existing student data
$stmt = $conn->prepare("
SELECT users.*, student_profiles.*
FROM users
JOIN student_profiles ON users.id = student_profiles.user_id
WHERE users.id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    echo "Student not found.";
    exit();
}

// Update student
if ($_SERVER["REQUEST_METHOD"] == "POST") {

$name = trim($_POST["name"]);
$email = trim($_POST["email"]);
$parent_email = trim($_POST["parent_email"]);
$teacher_email = trim($_POST["teacher_email"]);

$register_number = trim($_POST["register_number"]);
$department = trim($_POST["department"]);
$year = intval($_POST["year"]);
$room_number = trim($_POST["room_number"]);

$update_user = $conn->prepare("
UPDATE users
SET name=?, email=?, parent_email=?, teacher_email=?
WHERE id=?
");

$update_user->bind_param("ssssi",$name,$email,$parent_email,$teacher_email,$user_id);
$update_user->execute();

$update_profile = $conn->prepare("
UPDATE student_profiles
SET register_number=?, department=?, year=?, room_number=?
WHERE user_id=?
");

$update_profile->bind_param("ssisi",$register_number,$department,$year,$room_number,$user_id);
$update_profile->execute();

header("Location: view_students.php");
exit();
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Edit Student</title>

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

/* NAVBAR */

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
}

.logout-btn{
padding:8px 16px;
border-radius:8px;
background:#111;
color:#fff;
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
max-width:500px;
margin:auto;
padding:40px;
background:#fff;
border-radius:20px;

box-shadow:
0 40px 90px rgba(0,0,0,0.12),
0 15px 35px rgba(0,0,0,0.08);
}

h1{
margin-bottom:25px;
}

/* FORM */

label{
display:block;
margin-bottom:6px;
font-size:14px;
color:#555;
}

input,select{
width:100%;
padding:10px;
margin-bottom:16px;

border:1px solid #ddd;
border-radius:8px;
font-size:14px;
}

input:focus,select:focus{
outline:none;
border-color:#111;
}

/* BUTTON */

button{
background:#111;
color:white;
padding:12px;
border:none;
border-radius:8px;
cursor:pointer;
width:100%;
font-size:14px;
}

button:hover{
opacity:0.9;
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

<h1>Edit Student</h1>

<form method="POST">

<label>Name</label>
<input type="text" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>

<label>Register Number</label>
<input type="text" name="register_number" value="<?php echo htmlspecialchars($student['register_number']); ?>" required>

<label>Department</label>
<select name="department" required>
<option value="BCA">BCA</option>
<option value="BBA">BBA</option>
<option value="BCom">BCom</option>
<option value="BA English">BA English</option>
<option value="BA Economics">BA Economics</option>
<option value="BSc Computer Science">BSc Computer Science</option>
<option value="BSc Mathematics">BSc Mathematics</option>
<option value="BSc Physics">BSc Physics</option>
</select>

<label>Year</label>
<input type="number" name="year" value="<?php echo htmlspecialchars($student['year']); ?>" required>

<label>Room Number</label>
<input type="text" name="room_number" value="<?php echo htmlspecialchars($student['room_number']); ?>" required>

<label>Student Email</label>
<input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>">

<label>Parent Email</label>
<input type="email" name="parent_email" value="<?php echo htmlspecialchars($student['parent_email']); ?>">

<label>Teacher Email</label>
<input type="email" name="teacher_email" value="<?php echo htmlspecialchars($student['teacher_email']); ?>">

<button type="submit">Update Student</button>

</form>

<a class="back-btn" href="view_students.php">← Back to Students</a>

</div>

</body>
</html>