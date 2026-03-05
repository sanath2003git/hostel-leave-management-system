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

$stmt = $conn->prepare("
    UPDATE hostel_leaves
    SET returned_at = NOW()
    WHERE id = ?
");

$stmt->bind_param("i", $leave_id);
$stmt->execute();

header("Location: view_requests.php");
exit();
?>