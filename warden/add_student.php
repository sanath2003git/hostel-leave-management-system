<?php
include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST["name"];
    $register_number = $_POST["register_number"];
    $username = $register_number;

    $email = $_POST["email"];
    $parent_email = $_POST["parent_email"];
    $teacher_email = $_POST["teacher_email"];

    $department = $_POST["department"];
    $year = $_POST["year"];
    $room_number = $_POST["room_number"];
    $phone = $_POST["phone"];

    $password_plain = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"),0,8);
    $password = password_hash($password_plain, PASSWORD_DEFAULT);

    $role = $conn->query("SELECT id FROM roles WHERE role_name='student'");
    $role_id = $role->fetch_assoc()["id"];

    $stmt = $conn->prepare("
        INSERT INTO users (role_id, name, username, password, email, parent_email, teacher_email)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("issssss", $role_id, $name, $username, $password, $email, $parent_email, $teacher_email);
    $stmt->execute();

    $user_id = $conn->insert_id;

    $stmt2 = $conn->prepare("
        INSERT INTO student_profiles
        (user_id, register_number, department, year, room_number, phone)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt2->bind_param("ississ", $user_id, $register_number, $department, $year, $room_number, $phone);
    $stmt2->execute();

    include("../config/mail_config.php");

    $subject = "Hostel Leave System Login Details";

    $body = "
Hello $name,<br><br>

Your account has been created in the <b>Hostel Leave Management System</b>.<br><br>

<b>Login Details</b><br>
Username: $username<br>
Password: $password_plain<br><br>

Please login and change your password.<br><br>

Regards,<br>
Hostel Administration
";

    sendMail($email, $subject, $body);

    $message = "Student added successfully and login credentials sent to email.";
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Add Student</title>

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
margin-bottom:5px;
font-size:14px;
}

input,select{
width:100%;
padding:10px;
margin-bottom:15px;
border:1px solid #ddd;
border-radius:8px;
font-size:14px;
}

button{
background:#111;
color:white;
padding:12px;
border:none;
border-radius:8px;
cursor:pointer;
width:100%;
}

button:hover{
opacity:0.9;
}

/* SUCCESS MESSAGE */

.success{
color:green;
margin-bottom:15px;
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

<h1>Add Student</h1>

<?php if($message){ ?>
<p class="success"><?php echo $message; ?></p>
<?php } ?>

<form method="POST">

<label>Name</label>
<input type="text" name="name" required>

<label>Register Number</label>
<input type="text" name="register_number" required>

<label>Email</label>
<input type="email" name="email" required>

<label>Parent Email</label>
<input type="email" name="parent_email">

<label>Teacher Email</label>
<input type="email" name="teacher_email">

<label>Department</label>
<select name="department" required>
<option value="">Select Department</option>
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
<select name="year">
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
</select>

<label>Room Number</label>
<input type="text" name="room_number" required>

<label>Student Phone</label>
<input type="text" name="phone" required>

<button type="submit">Add Student</button>

</form>

<a class="back-btn" href="dashboard.php">← Back to Dashboard</a>

</div>

</body>
</html>