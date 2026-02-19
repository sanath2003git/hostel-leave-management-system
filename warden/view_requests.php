<?php
include("../includes/auth_check.php");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}

$leaves = $_SESSION["leaves"] ?? [];
?>

<h2>Leave Requests</h2>

<?php
if (empty($leaves)) {
    echo "No leave requests.";
} else {
    foreach ($leaves as $index => $leave) {
        echo "<hr>";
        echo "Student: " . $leave["student"] . "<br>";
        echo "Type: " . $leave["leave_type"] . "<br>";
        echo "From: " . $leave["from_date"] . "<br>";
        echo "To: " . $leave["to_date"] . "<br>";
        echo "Reason: " . $leave["reason"] . "<br>";
        echo "Status: " . $leave["status"] . "<br>";

        if ($leave["status"] == "Pending") {
            echo "<a href='approve_leave.php?id=$index&action=approve'>Approve</a> | ";
            echo "<a href='approve_leave.php?id=$index&action=reject'>Reject</a>";
        }
    }
}
?>

<br><br>
<a href="dashboard.php">Back</a>