<?php
include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "student") {
    echo "Access Denied";
    exit();
}

$user_id = $_SESSION["user_id"];
$success = "";
$error = "";

/* UPDATE PASSWORD */
if(isset($_POST["update_password"])){

    $new_password = trim($_POST["new_password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    if($new_password == "" || $confirm_password == ""){
        $error = "Please fill all password fields.";
    }
    elseif(strlen($new_password) < 8){
        $error = "Password must be at least 8 characters.";
    }
    elseif(preg_match('/\s/', $new_password)){
        $error = "Password must not contain spaces.";
    }
    elseif(!preg_match('/[A-Z]/', $new_password)){
        $error = "Password must contain at least one uppercase letter.";
    }
    elseif(!preg_match('/[a-z]/', $new_password)){
        $error = "Password must contain at least one lowercase letter.";
    }
    elseif(!preg_match('/[0-9]/', $new_password)){
        $error = "Password must contain at least one number.";
    }
    elseif(!preg_match('/[\W_]/', $new_password)){
        $error = "Password must contain at least one special character.";
    }
    elseif($new_password != $confirm_password){
        $error = "Passwords do not match.";
    }
    else{

        /* HASHED PASSWORD UPDATE */
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
        UPDATE users
        SET password = ?
        WHERE id = ?
        ");

        $stmt->bind_param("si", $hashed_password, $user_id);
        $stmt->execute();

        $success = "Password updated successfully.";
    }
}

/* FETCH PROFILE */
$stmt = $conn->prepare("
SELECT 
    users.name,
    users.email,
    users.parent_email,
    users.teacher_email,
    student_profiles.register_number,
    student_profiles.department,
    student_profiles.year,
    student_profiles.room_number,
    student_profiles.phone
FROM users
JOIN student_profiles
ON users.id = student_profiles.user_id
WHERE users.id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$student = $result->fetch_assoc();

$student_name = isset($student["name"]) ? $student["name"] : $_SESSION["username"];
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Profile</title>

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
background:linear-gradient(135deg,#dcdde1 0%,#eceef2 40%,#d6d8de 100%);
padding:120px 60px 60px 60px;
}

.topbar{
position:fixed;
top:0;
left:0;
width:100%;
height:70px;
background:#111;
color:#fff;
display:flex;
justify-content:space-between;
align-items:center;
padding:0 60px;
z-index:1000;
}

.topbar a{
color:#fff;
text-decoration:none;
margin-left:20px;
}

.dashboard{
max-width:760px;
margin:auto;
padding:60px;
background:#fff;
border-radius:22px;
box-shadow:
0 30px 80px rgba(0,0,0,0.12),
0 10px 25px rgba(0,0,0,0.08);
}

h2{
font-size:28px;
margin-bottom:30px;
}

.row{
margin-bottom:16px;
padding-bottom:10px;
border-bottom:1px solid #f1f1f1;
font-size:15px;
}

.label{
font-weight:600;
color:#111;
margin-right:8px;
}

.section{
margin-top:35px;
padding-top:25px;
border-top:1px solid #eee;
}

input{
width:100%;
padding:12px;
margin-top:10px;
margin-bottom:14px;
border:1px solid #ddd;
border-radius:8px;
outline:none;
}

button{
padding:11px 18px;
border:none;
border-radius:8px;
background:#111;
color:#fff;
cursor:pointer;
}

button:hover{
background:#444;
}

.msg{
margin-bottom:15px;
padding:12px;
border-radius:8px;
font-size:14px;
}

.success{
background:#eafaf1;
color:#27ae60;
}

.error{
background:#fff0f0;
color:#e74c3c;
}

.note{
font-size:13px;
color:#666;
margin-bottom:15px;
line-height:1.6;
}

.back-btn{
display:inline-block;
margin-top:25px;
padding:11px 16px;
background:#111;
color:#fff;
text-decoration:none;
border-radius:8px;
}

.back-btn:hover{
background:#444;
}

</style>

</head>

<body>

<div class="topbar">

<div>Hostel Leave System</div>

<div>
<?php echo htmlspecialchars($student_name); ?> |
<a href="../auth/logout.php">Logout</a>
</div>

</div>

<div class="dashboard">

<h2>Student Profile</h2>

<div class="row"><span class="label">Name:</span> <?php echo htmlspecialchars($student["name"]); ?></div>
<div class="row"><span class="label">Register Number:</span> <?php echo htmlspecialchars($student["register_number"]); ?></div>
<div class="row"><span class="label">Department:</span> <?php echo htmlspecialchars($student["department"]); ?></div>
<div class="row"><span class="label">Year:</span> <?php echo htmlspecialchars($student["year"]); ?></div>
<div class="row"><span class="label">Room Number:</span> <?php echo htmlspecialchars($student["room_number"]); ?></div>
<div class="row"><span class="label">Phone:</span> <?php echo htmlspecialchars($student["phone"]); ?></div>
<div class="row"><span class="label">Email:</span> <?php echo htmlspecialchars($student["email"]); ?></div>
<div class="row"><span class="label">Parent Email:</span> <?php echo htmlspecialchars($student["parent_email"]); ?></div>
<div class="row"><span class="label">Teacher Email:</span> <?php echo htmlspecialchars($student["teacher_email"]); ?></div>

<div class="section">

<h2 style="font-size:22px;">Update Password</h2>

<div class="note">
Password must contain minimum 8 characters, uppercase, lowercase, number and special character.
</div>

<?php if($success != ""){ ?>
<div class="msg success"><?php echo $success; ?></div>
<?php } ?>

<?php if($error != ""){ ?>
<div class="msg error"><?php echo $error; ?></div>
<?php } ?>

<form method="POST">

<input type="password" name="new_password" placeholder="New Password">

<input type="password" name="confirm_password" placeholder="Confirm Password">

<button type="submit" name="update_password">Update Password</button>

</form>

</div>

<a class="back-btn" href="dashboard.php">← Back to Dashboard</a>

</div>

</body>
</html>