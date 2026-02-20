<?php
include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}

if (isset($_GET["id"]) && isset($_GET["action"])) {

    $leave_id = $_GET["id"];
    $action = $_GET["action"];

    if ($action == "approve") {
        $status = "Approved";
    } elseif ($action == "reject") {
        $status = "Rejected";
    } else {
        header("Location: view_requests.php");
        exit();
    }

    $query = "UPDATE hostel_leaves 
              SET status='$status' 
              WHERE id='$leave_id'";

    mysqli_query($conn, $query);
}

header("Location: view_requests.php");
exit();
?>