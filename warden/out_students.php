<?php
include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}

$current_time = date("Y-m-d H:i:s");

$query = "SELECT hostel_leaves.*, users.name 
          FROM hostel_leaves
          JOIN users ON hostel_leaves.student_id = users.id
          WHERE hostel_leaves.status = 'Approved'
          AND '$current_time' BETWEEN hostel_leaves.from_datetime 
          AND hostel_leaves.to_datetime";

$result = mysqli_query($conn, $query);
?>

<h2>Students Currently Outside</h2>

<?php
if (mysqli_num_rows($result) == 0) {
    echo "No students currently outside.";
} else {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<hr>";
        echo "Student: " . $row["name"] . "<br>";
        echo "From: " . $row["from_datetime"] . "<br>";
        echo "To: " . $row["to_datetime"] . "<br>";
    }
}
?>

<br><br>
<a href="dashboard.php">Back</a>