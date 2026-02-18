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
<a href="../auth/logout.php">Logout</a>