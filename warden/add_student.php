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

    /* Register number used as username */
    $username = $register_number;

    $email = $_POST["email"];
    $parent_email = $_POST["parent_email"];
    $teacher_email = $_POST["teacher_email"];

    $department = $_POST["department"];
    $year = $_POST["year"];
    $room_number = $_POST["room_number"];
    $phone = $_POST["phone"];

    /* Generate random password */
    $password_plain = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"),0,8);

    /* Hash password */
    $password = password_hash($password_plain, PASSWORD_DEFAULT);

    /* Get student role id */
    $role = $conn->query("SELECT id FROM roles WHERE role_name='student'");
    $role_id = $role->fetch_assoc()["id"];

    /* Insert user */
    $stmt = $conn->prepare("
        INSERT INTO users (role_id, name, username, password, email, parent_email, teacher_email)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("issssss", $role_id, $name, $username, $password, $email, $parent_email, $teacher_email);
    $stmt->execute();

    $user_id = $conn->insert_id;

    /* Insert student profile */
    $stmt2 = $conn->prepare("
        INSERT INTO student_profiles
        (user_id, register_number, department, year, room_number, phone)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt2->bind_param("ississ", $user_id, $register_number, $department, $year, $room_number, $phone);
    $stmt2->execute();

    /* Send email */
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

<style>

body{
font-family:Arial;
background:#f4f4f4;
padding:40px;
}

.form-box{
background:white;
padding:30px;
border-radius:8px;
width:450px;
box-shadow:0 3px 10px rgba(0,0,0,0.1);
}

input,select{
width:100%;
padding:8px;
margin-top:5px;
margin-bottom:15px;
}

button{
background:#2c3e50;
color:white;
padding:10px 15px;
border:none;
cursor:pointer;
}

.success{
color:green;
margin-bottom:15px;
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

<div class="form-box">

<h2>Add Student</h2>

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

<a class="back-btn" href="dashboard.php">Back to Dashboard</a>

</div>

</body>
</html>