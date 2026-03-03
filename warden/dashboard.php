<?php
include("../includes/auth_check.php");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}
?>

<h2>Warden Dashboard</h2>
<p>Welcome, <?php echo $_SESSION["username"]; ?></p>

<a href="view_requests.php">View Leave Requests</a><br><br>
<a href="out_students.php">View Out Students</a><br><br>
<a href="reports.php">View Reports</a><br><br>
<a href="add_student.php">Add Student</a><br><br>
<a href="view_students.php">View Students</a><br><br>
<a href="../auth/logout.php">Logout</a>