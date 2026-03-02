<?php
include("../includes/auth_check.php");

if ($_SESSION["role"] != "student") {
    echo "Access Denied";
    exit();
}
?>

<h2>Student Dashboard</h2>
<p>Welcome, <?php echo $_SESSION["username"]; ?></p>

<a href="apply_leave.php">Apply Leave</a><br><br>
<a href="leave_status.php">View Leave Status</a><br><br>
<a href="leave_history.php">Leave History</a><br><br>
<a href="../auth/logout.php">Logout</a>