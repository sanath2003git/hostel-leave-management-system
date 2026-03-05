<?php

include("../includes/auth_check.php");

include("../config/db.php");

if ($_SESSION["role"] != "warden") {

    echo "Access Denied";

    exit();

}

/* JOIN users and leave_types */

$query = "

SELECT hostel_leaves.*, users.name, leave_types.type_name

FROM hostel_leaves

JOIN users 

    ON hostel_leaves.student_id = users.id

JOIN leave_types 

    ON hostel_leaves.leave_type_id = leave_types.id

ORDER BY hostel_leaves.applied_at DESC

";

$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>

<html>

<head>

<title>Leave Requests</title>

<style>

body{

    font-family: Arial;

    padding:40px;

    background:#f5f5f5;

}

h2{

    margin-bottom:25px;

}

.card{

    background:white;

    padding:20px;

    margin-bottom:20px;

    border-radius:6px;

    box-shadow:0 2px 6px rgba(0,0,0,0.1);

}

.actions a{

    text-decoration:none;

    padding:6px 10px;

    margin-right:10px;

    border-radius:4px;

    font-size:14px;

}

.approve{

    background:#28a745;

    color:white;

}

.reject{

    background:#dc3545;

    color:white;

}

.return{

    background:#007bff;

    color:white;

}

.status{

    font-weight:bold;

}

</style>

</head>

<body>

<h2>Leave Requests</h2>

<?php

if (mysqli_num_rows($result) == 0) {

    echo "No leave requests.";

} else {

    while ($row = mysqli_fetch_assoc($result)) {

        echo "<div class='card'>";

        echo "<strong>Student:</strong> " . htmlspecialchars($row["name"]) . "<br>";

        echo "<strong>Leave Type:</strong> " . htmlspecialchars($row["type_name"]) . "<br>";

        echo "<strong>From:</strong> " . htmlspecialchars($row["from_datetime"]) . "<br>";

        echo "<strong>To:</strong> " . htmlspecialchars($row["to_datetime"]) . "<br>";

        echo "<strong>Reason:</strong> " . htmlspecialchars($row["reason"]) . "<br>";

        echo "<strong>Status:</strong> <span class='status'>" . htmlspecialchars($row["status"]) . "</span><br>";

        if ($row["returned_at"] != NULL) {

            echo "<strong>Returned At:</strong> " . htmlspecialchars($row["returned_at"]) . "<br>";

        }

        echo "<br><div class='actions'>";

        /* Pending requests */

        if ($row["status"] == "Pending") {

            echo "<a class='approve' href='approve_leave.php?id=".$row["id"]."&action=approve'>Approve</a>";

            echo "<a class='reject' href='approve_leave.php?id=".$row["id"]."&action=reject'>Reject</a>";

        }

        /* Approved but not returned */

        if ($row["status"] == "Approved" && $row["returned_at"] == NULL) {

            echo "<a class='return' href='mark_returned.php?id=".$row["id"]."'>Mark Returned</a>";

        }

        echo "</div>";

        echo "</div>";

    }

}

?>

<br>

<a href="dashboard.php">Back to Dashboard</a>

</body>

</html>