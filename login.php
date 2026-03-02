<?php
session_start();
include("config.php");

if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users 
            WHERE username='$username' 
            AND password='$password'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        $user = $result->fetch_assoc();

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role_id'] = $user['role_id'];

        if ($user['role_id'] == 1) {
            header("Location: student_dashboard.php");
        } else if ($user['role_id'] == 2) {
            header("Location: warden_dashboard.php");
        }

        exit();

    } else {
        echo "<p style='color:red;'>Invalid Login!</p>";
    }
}
?>

<h2>Login</h2>

<form method="POST">
    Username: <input type="text" name="username" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    <button type="submit" name="login">Login</button>
</form>