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

    // ✅ Update leave status + mess cut
    $update = $conn->prepare("
        UPDATE hostel_leaves 
        SET status = 'Approved', mess_cut = 1
        WHERE id = ?
    ");
    $update->bind_param("i", $leave_id);
    $update->execute();

    // ✅ Get student details
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

    // ✅ Email content
    $subject = "Leave Approved - Hostel Leave System";

    $body = "
        <h3>Leave Approved</h3>
        <p><strong>Student:</strong> {$data['name']}</p>
        <p><strong>From:</strong> {$data['from_datetime']}</p>
        <p><strong>To:</strong> {$data['to_datetime']}</p>
        <p><strong>Status:</strong> Approved</p>
        <p><strong>Mess Cut:</strong> Enabled</p>
    ";

    // ✅ Send email to all
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

    // ❌ Reject leave (no mess cut)
    $update = $conn->prepare("
        UPDATE hostel_leaves 
        SET status = 'Rejected', mess_cut = 0
        WHERE id = ?
    ");
    $update->bind_param("i", $leave_id);
    $update->execute();

    header("Location: view_requests.php");
    exit();
}
?>