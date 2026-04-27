<?php
include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}

if (!isset($_GET["id"])) {
    echo "Invalid request";
    exit();
}

$leave_id = intval($_GET["id"]);

/* Mark student returned + disable mess cut */

$stmt = $conn->prepare("
    UPDATE hostel_leaves
    SET returned_at = NOW(),
        mess_cut = 0
    WHERE id = ?
");

$stmt->bind_param("i", $leave_id);

if ($stmt->execute()) {
    header("Location: view_requests.php");
    exit();
} else {
    echo "Error updating return status.";
}

$stmt->close();
$conn->close();
?>