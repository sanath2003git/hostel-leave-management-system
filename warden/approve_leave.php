<?php
include("../includes/auth_check.php");
include("../config/db.php");
include("../config/mail_config.php");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}

if (!isset($_GET["id"]) || !isset($_GET["action"])) {
    echo "Invalid Request";
    exit();
}

$leave_id = intval($_GET["id"]);
$action = $_GET["action"];

if ($action == "approve") {

    // Update leave status
    $update = $conn->prepare("
        UPDATE hostel_leaves 
        SET status = 'Approved'
        WHERE id = ?
    ");
    $update->bind_param("i", $leave_id);
    $update->execute();

    // Get student and email details
    $query = $conn->prepare("
        SELECT users.name, users.email, users.parent_email, users.teacher_email,
               hostel_leaves.from_datetime, hostel_leaves.to_datetime
        FROM hostel_leaves
        JOIN users ON hostel_leaves.student_id = users.id
        WHERE hostel_leaves.id = ?
    ");
    $query->bind_param("i", $leave_id);
    $query->execute();
    $result = $query->get_result();
    $data = $result->fetch_assoc();

    $subject = "Leave Approved - Hostel Leave System";

    $body = "
        <h3>Leave Approved</h3>
        <p><strong>Student:</strong> {$data['name']}</p>
        <p><strong>From:</strong> {$data['from_datetime']}</p>
        <p><strong>To:</strong> {$data['to_datetime']}</p>
        <p>Status: Approved</p>
    ";

    $emails = [
        $data["email"],
        $data["parent_email"],
        $data["teacher_email"]
    ];

    foreach ($emails as $recipient) {
        if (!empty($recipient)) {
            sendMail($recipient, $subject, $body);
        }
    }

    header("Location: view_requests.php");
    exit();
}

if ($action == "reject") {

    $update = $conn->prepare("
        UPDATE hostel_leaves 
        SET status = 'Rejected'
        WHERE id = ?
    ");
    $update->bind_param("i", $leave_id);
    $update->execute();

    header("Location: view_requests.php");
    exit();
}
?>