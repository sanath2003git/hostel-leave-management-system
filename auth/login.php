<?php
session_start();

$users = [
    "student1" => ["password" => "1234", "role" => "student"],
    "warden1" => ["password" => "1234", "role" => "warden"]
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    if (isset($users[$username]) && $users[$username]["password"] == $password) {
        $_SESSION["username"] = $username;
        $_SESSION["role"] = $users[$username]["role"];

        if ($_SESSION["role"] == "student") {
            header("Location: ../student/dashboard.php");
        } else {
            header("Location: ../warden/dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
<h2>Login</h2>

<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

<form method="POST">
    Username: <input type="text" name="username" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    <button type="submit">Login</button>
</form>
</body>
</html>