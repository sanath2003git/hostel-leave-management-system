<?php
include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}

$message = "";
$error = false;

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

    // DUPLICATE CHECK
    $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check->bind_param("s", $register_number);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $message = "User already added!";
        $error = true;
    } else {

        $password_plain = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"),0,8);
        $password = password_hash($password_plain, PASSWORD_DEFAULT);

        $role = $conn->query("SELECT id FROM roles WHERE role_name='student'");
        $role_id = $role->fetch_assoc()["id"];

        // INSERT USERS
        $stmt = $conn->prepare("
            INSERT INTO users (role_id, name, username, password, email, parent_email, teacher_email)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("issssss", $role_id, $name, $username, $password, $email, $parent_email, $teacher_email);
        $stmt->execute();

        $user_id = $conn->insert_id;

        // INSERT PROFILE
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
Your account has been created.<br><br>
<b>Username:</b> $username<br>
<b>Password:</b> $password_plain<br><br>
Please login and change your password.
";

        sendMail($email, $subject, $body);

        $message = "Student added successfully!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Add Student</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}

body{
min-height:100vh;
background: linear-gradient(135deg,#e8eaef 0%,#f4f5f8 40%,#e6e8ed 100%);
padding:120px 60px 60px 60px;
}

.topbar{
position:fixed;top:0;left:0;width:100%;height:70px;
background:#111;display:flex;justify-content:space-between;align-items:center;
padding:0 60px;color:#fff;z-index:1000;
}

.container{
max-width:500px;margin:auto;padding:40px;background:#fff;border-radius:20px;
box-shadow:0 20px 40px rgba(0,0,0,0.1);
}

h1{margin-bottom:20px;}

label{display:block;margin-top:10px;font-size:14px;}

input,select{
width:100%;padding:10px;margin-top:5px;
border:1px solid #ddd;border-radius:8px;
}

button{
margin-top:15px;background:#111;color:#fff;
padding:12px;border:none;border-radius:8px;width:100%;
}

.success {
    background: #2ecc71;
    color: white;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
    text-align: center;
}

.error {
    background: #e74c3c;
    color: white;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
    text-align: center;
}

/* 🔥 BACK BUTTON */
.back-btn {
    display: inline-block;
    margin-top: 15px;
    padding: 10px 14px;
    border-radius: 8px;
    text-decoration: none;
    color: #fff;
    background: #111;
    text-align: center;
    width: 100%;
    transition: 0.2s;
}

.back-btn:hover {
    background: #444;
}
</style>

</head>

<body>

<div class="topbar">
<div>Hostel Leave System</div>
<div><?php echo $_SESSION["username"]; ?></div>
</div>

<div class="container">

<h1>Add Student</h1>

<?php if($message){ ?>
<p class="<?php echo $error ? 'error' : 'success'; ?>">
    <?php echo $message; ?>
</p>
<?php } ?>

<form method="POST">

<label>Register Number</label>
<input type="text" id="reg_no" name="register_number" required>

<label>Name</label>
<input type="text" id="name" name="name" required>

<label>Email</label>
<input type="email" id="email" name="email" required>

<label>Parent Email</label>
<input type="email" id="parent_email" name="parent_email">

<label>Teacher Email</label>
<input type="email" id="teacher_email" name="teacher_email">

<label>Department</label>
<select id="department" name="department" required>
<option value="">Select</option>
<option value="BCA">BCA</option>
<option value="BBA">BBA</option>
<option value="BCom">BCom</option>
<option value="BA English">BA English</option>
<option value="BA Economics">BA Economics</option>
<option value="BSc Computer Science">BSc Computer Science</option>
<option value="BSc Mathematics">BSc Mathematics</option>
<option value="BSc Physics">BSc Physics</option>
<option value="MCA">MCA</option>
</select>

<label>Year</label>
<select id="year" name="year">
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
</select>

<label>Room Number</label>
<input type="text" id="room_number" name="room_number" required>

<label>Phone</label>
<input type="text" id="phone" name="phone" required>

<button type="submit">Add Student</button>

</form>

<!-- 🔥 BACK BUTTON -->
<a href="dashboard.php" class="back-btn">← Back to Dashboard</a>

</div>

<!-- 🔥 AUTO-FILL SCRIPT -->
<script>
document.getElementById("reg_no").addEventListener("keyup", function() {
    let reg = this.value;

    if(reg.length < 2) return;

    fetch("fetch_student.php?reg_no=" + reg)
    .then(res => res.json())
    .then(data => {
        document.getElementById("name").value = data.name || "";
        document.getElementById("email").value = data.email || "";
        document.getElementById("parent_email").value = data.parent_email || "";
        document.getElementById("teacher_email").value = data.teacher_email || "";
        document.getElementById("department").value = data.department || "";
        document.getElementById("year").value = data.year || "";
        document.getElementById("phone").value = data.phone || "";
    });
});
</script>

</body>
</html>