<?php
include("../includes/auth_check.php");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}

if (isset($_GET["id"]) && isset($_GET["action"])) {

    $id = $_GET["id"];
    $action = $_GET["action"];

    if (isset($_SESSION["leaves"][$id])) {
        if ($action == "approve") {
            $_SESSION["leaves"][$id]["status"] = "Approved";
        } elseif ($action == "reject") {
            $_SESSION["leaves"][$id]["status"] = "Rejected";
        }
    }
}

header("Location: view_requests.php");
exit();
?>