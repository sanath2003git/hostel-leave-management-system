<?php
include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}

/* ✅ JOIN leave_types */
$query = "
    SELECT hostel_leaves.*, users.name, leave_types.type_name
    FROM hostel_leaves
    JOIN users 
        ON hostel_leaves.student_id = users.id
    JOIN leave_types 
        ON hostel_leaves.leave_type_id = leave_types.id
    WHERE hostel_leaves.status = 'Pending'
    ORDER BY hostel_leaves.applied_at DESC
";

$result = mysqli_query($conn, $query);
?>

<h2>Pending Leave Requests</h2>

<?php
if (mysqli_num_rows($result) == 0) {
    echo "No pending leave requests.";
} else {

    while ($row = mysqli_fetch_assoc($result)) {

        echo "<hr>";

        echo "<strong>Student:</strong> " . htmlspecialchars($row["name"]) . "<br>";
        echo "<strong>Leave Type:</strong> " . htmlspecialchars($row["type_name"]) . "<br>";
        echo "<strong>From:</strong> " . htmlspecialchars($row["from_datetime"]) . "<br>";
        echo "<strong>To:</strong> " . htmlspecialchars($row["to_datetime"]) . "<br>";
        echo "<strong>Reason:</strong> " . htmlspecialchars($row["reason"]) . "<br>";
        echo "<strong>Status:</strong> " . htmlspecialchars($row["status"]) . "<br><br>";

        echo "<a href='approve_leave.php?id=" . $row["id"] . "&action=approve'>Approve</a> | ";
        echo "<a href='approve_leave.php?id=" . $row["id"] . "&action=reject'>Reject</a>";
    }
}
?>

<br><br>
<a href="dashboard.php">Back</a>