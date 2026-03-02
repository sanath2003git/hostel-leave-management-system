<?php
include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "student") {
    echo "Access Denied";
    exit();
}

$student_id = $_SESSION["user_id"];

$query = "SELECT * FROM hostel_leaves 
          WHERE student_id = '$student_id'
          ORDER BY applied_at DESC
          LIMIT 1";

$result = mysqli_query($conn, $query);
?>

<h2>Latest Leave Status</h2>

<?php
if (mysqli_num_rows($result) == 0) {
    echo "No leave applications found.";
} else {
    $row = mysqli_fetch_assoc($result);

    echo "Leave Type: " . $row["leave_type"] . "<br>";
    echo "From: " . $row["from_datetime"] . "<br>";
    echo "To: " . $row["to_datetime"] . "<br>";
    echo "Reason: " . $row["reason"] . "<br>";
    echo "Status: <strong>" . $row["status"] . "</strong><br>";
}
?>

<br><br>
<a href="dashboard.php">Back</a>