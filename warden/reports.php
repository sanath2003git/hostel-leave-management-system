<?php
include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}

$total = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT COUNT(*) as count FROM hostel_leaves"));

$approved = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT COUNT(*) as count FROM hostel_leaves WHERE status='Approved'"));

$rejected = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT COUNT(*) as count FROM hostel_leaves WHERE status='Rejected'"));

$pending = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT COUNT(*) as count FROM hostel_leaves WHERE status='Pending'"));
?>

<h2>Leave Reports</h2>

Total Leaves: <?php echo $total["count"]; ?><br><br>
Approved Leaves: <?php echo $approved["count"]; ?><br><br>
Rejected Leaves: <?php echo $rejected["count"]; ?><br><br>
Pending Leaves: <?php echo $pending["count"]; ?><br><br>

<a href="dashboard.php">Back</a>