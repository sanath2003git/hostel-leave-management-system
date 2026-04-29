<?php
session_start();
include("../config/db.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $stmt = $conn->prepare("
        SELECT users.*, roles.role_name 
        FROM users 
        JOIN roles ON users.role_id = roles.id 
        WHERE users.username = ?
        LIMIT 1
    ");

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {

            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role_name"];

            if ($user["role_name"] === "student") {
                header("Location: ../student/dashboard.php");
            } elseif ($user["role_name"] === "warden") {
                header("Location: ../warden/dashboard.php");
            }
            exit();

        } else {
            $error = "Invalid username or password";
        }

    } else {
        $error = "Invalid username or password";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Poppins',sans-serif;
}

body{
background:#f3f3f3;
display:flex;
justify-content:center;
align-items:center;
min-height:100vh;
padding-top:80px;
}

/* NAVBAR */

.navbar{
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
padding:0 50px;
z-index:1000;
box-shadow:0 8px 20px rgba(0,0,0,0.15);
}

.logo{
font-size:22px;
font-weight:600;
letter-spacing:0.5px;
}

.tagline{
font-size:13px;
color:#ddd;
}

/* LOGIN BOX */

.login-wrapper{
width:900px;
height:550px;
display:flex;
background:#fff;
border-radius:18px;
overflow:hidden;
box-shadow:0 20px 40px rgba(0,0,0,0.15);
}

.login-left{
width:50%;
padding:60px;
display:flex;
flex-direction:column;
justify-content:center;
}

.login-left h2{
font-size:28px;
margin-bottom:10px;
}

.login-left p{
color:#777;
margin-bottom:30px;
font-size:14px;
}

.form-group{
margin-bottom:20px;
}

.form-group label{
font-size:13px;
color:#555;
display:block;
margin-bottom:6px;
}

.form-group input{
width:100%;
padding:12px;
border:1px solid #ddd;
border-radius:10px;
outline:none;
font-size:15px;
}

.form-group input:focus{
border-color:#111;
}

/* PASSWORD */

.password-box{
position:relative;
width:100%;
}

.password-box input{
padding-right:45px;
}

.eye-btn{
position:absolute;
right:15px;
top:50%;
transform:translateY(-50%);
cursor:pointer;
font-size:18px;
user-select:none;
}

/* OPTIONS */

.form-options{
display:flex;
justify-content:space-between;
font-size:13px;
margin-bottom:20px;
}

.form-options a{
text-decoration:none;
color:#000;
}

/* BUTTON */

.btn-login{
width:100%;
padding:12px;
background:#000;
color:#fff;
border:none;
border-radius:10px;
cursor:pointer;
font-size:15px;
font-weight:500;
transition:0.3s;
}

.btn-login:hover{
background:#333;
}

/* ERROR */

.error-msg{
color:red;
font-size:13px;
margin-bottom:15px;
}

/* IMAGE */

.login-right{
width:50%;
background:url('../assets/images/tkm.jpg') center/cover no-repeat;
}

/* MOBILE */

@media(max-width:900px){

.login-wrapper{
flex-direction:column;
width:95%;
height:auto;
}

.login-left,
.login-right{
width:100%;
}

.login-right{
height:260px;
}

.navbar{
padding:0 20px;
}

.logo{
font-size:16px;
}

.tagline{
display:none;
}

}

</style>
</head>

<body>

<!-- NAVBAR -->

<div class="navbar">
<div class="logo">Hostel Leave Management System</div>
<div class="tagline">MCA Project</div>
</div>

<!-- LOGIN -->

<div class="login-wrapper">

<div class="login-left">

<h2>Welcome back</h2>
<p>Please enter your details.</p>

<?php if(!empty($error)): ?>
<div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="POST">

<div class="form-group">
<label>User Name</label>
<input type="text" name="username" required>
</div>

<div class="form-group">

<label>Password</label>

<div class="password-box">
<input type="password" name="password" id="password" placeholder="Enter Password" required>
<span class="eye-btn" id="eye" onclick="togglePassword()">👁</span>
</div>

</div>

<div class="form-options">
<label><input type="checkbox"> Remember me</label>
<a href="#">Forgot Password</a>
</div>

<button type="submit" class="btn-login">Submit</button>

</form>

</div>

<div class="login-right"></div>

</div>

<script>
function togglePassword(){

let password = document.getElementById("password");
let eye = document.getElementById("eye");

if(password.type === "password"){
password.type = "text";
eye.innerHTML = "🙈";
}else{
password.type = "password";
eye.innerHTML = "👁";
}

}
</script>

</body>
</html>