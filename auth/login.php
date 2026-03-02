<?php
session_start();
include("../config/db.php");

$error = "";  

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role"];

            if ($user["role"] == "student") {
                header("Location: ../student/dashboard.php");
            } else {
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f3f3f3;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-wrapper {
            width: 900px;
            height: 550px;
            display: flex;
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        /* LEFT SIDE */
        .login-left {
            width: 50%;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-left h2 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .login-left p {
            color: #777;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-size: 13px;
            color: #555;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-top: 5px;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .form-options a {
            text-decoration: none;
            color: #000;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: #000;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-login:hover {
            background: #333;
        }

        .extra-text {
            margin-top: 20px;
            font-size: 13px;
            color: #555;
        }

        .extra-text a {
            font-weight: 500;
            text-decoration: none;
            color: #000;
        }

        .login-right {
            width: 50%;
            background: url('../assets/images/tkm.jpg') center/cover no-repeat;
        }

        .feature {
            margin-bottom: 20px;
            font-size: 14px;
            color: #ccc;
        }

        .error-msg {
            color: red;
            font-size: 13px;
            margin-bottom: 15px;
        }

        @media(max-width: 900px) {
            .login-wrapper {
                flex-direction: column;
                width: 90%;
                height: auto;
            }

            .login-left,
            .login-right {
                width: 100%;
                padding: 40px;
            }
        }

    </style>
</head>
<body>

<div class="login-wrapper">

    <!-- LEFT -->
    <div class="login-left">

        <h2>Welcome back</h2>
        <p>Please enter your details.</p>

        <?php if(!empty($error)): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label>User Name</label>
                <input type="text" name="username" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-options">
                <label><input type="checkbox"> Remember me</label>
                <a href="#">Forgot Password</a>
            </div>

            <button type="submit" class="btn-login">Submit</button>

        </form>

        

    </div>

    <!-- RIGHT -->
<div class="login-right"></div>

</body>
</html>