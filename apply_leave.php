<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$leave_type = $_POST['leave_type'];
$from = $_POST['from_datetime'];
$to = $_POST['to_datetime'];
$reason = $_POST['reason'];

$sql = "INSERT INTO hostel_leaves 
        (student_id, leave_type_id, from_datetime, to_datetime, reason) 
        VALUES ('$student_id','$leave_type','$from','$to','$reason')";

$conn->query($sql);

header("Location: student_dashboard.php");
exit();
?>