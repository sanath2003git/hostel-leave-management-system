<?php
include("../includes/auth_check.php");
include("../config/db.php");

date_default_timezone_set("Asia/Kolkata");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}

if (!isset($_GET["id"])) {
    echo "Invalid Request";
    exit();
}

$leave_id = intval($_GET["id"]);

/* FETCH LEAVE DATA */
$stmt = $conn->prepare("
SELECT id, to_datetime
FROM hostel_leaves
WHERE id = ?
");

$stmt->bind_param("i", $leave_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Leave record not found.";
    exit();
}

$row = $result->fetch_assoc();

$current_time = date("Y-m-d H:i:s");
$to_datetime  = $row["to_datetime"];

/* CHECK RETURN TYPE */
if ($current_time < $to_datetime) {
    $return_status = "Returned Early";
}
elseif ($current_time > $to_datetime) {
    $return_status = "Returned Late";
}
else {
    $return_status = "Returned On Time";
}

/* UPDATE RETURN */
$update = $conn->prepare("
UPDATE hostel_leaves
SET returned_at = NOW(),
    mess_cut = 0,
    return_status = ?
WHERE id = ?
");

$update->bind_param("si", $return_status, $leave_id);
$update->execute();

/* REDIRECT */
header("Location: out_students.php?msg=returned");
exit();
?>