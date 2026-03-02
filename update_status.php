<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];
$status = $_GET['status'];

$conn->query("UPDATE hostel_leaves SET status='$status' WHERE id='$id'");

header("Location: warden_dashboard.php");
exit();
?>