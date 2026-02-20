<?php
session_start();

// If user already logged in
if (isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'student') {
        header("Location: student/dashboard.php");
        exit();
    }

    if ($_SESSION['role'] == 'warden') {
        header("Location: warden/dashboard.php");
        exit();
    }
}

// If not logged in
header("Location: auth/login.php");
exit();
?>