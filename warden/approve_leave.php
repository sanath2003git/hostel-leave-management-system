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
$action   = $_GET["action"];

/* FETCH LEAVE + STUDENT DETAILS */
$stmt = $conn->prepare("
SELECT hostel_leaves.*, users.name, users.email, users.parent_email, users.teacher_email
FROM hostel_leaves
JOIN users ON hostel_leaves.student_id = users.id
WHERE hostel_leaves.id = ?
");

$stmt->bind_param("i", $leave_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Leave request not found.";
    exit();
}

$data = $result->fetch_assoc();

/* APPROVE REQUEST */
if ($action == "approve") {

    $update = $conn->prepare("
    UPDATE hostel_leaves
    SET status='Approved',
        mess_cut=1
    WHERE id=?
    ");

    $update->bind_param("i", $leave_id);
    $update->execute();

    $subject = "Leave Approved - Hostel Leave System";

    $body = "
    <h3>Leave Approved</h3>
    <p><strong>Student:</strong> {$data['name']}</p>
    <p><strong>From:</strong> {$data['from_datetime']}</p>
    <p><strong>To:</strong> {$data['to_datetime']}</p>
    <p><strong>Status:</strong> Approved</p>
    <p>Mess cut has been activated automatically.</p>
    <p>Your leave request has been approved by the warden.</p>
    ";

    $emails = [
        $data["email"],
        $data["parent_email"],
        $data["teacher_email"]
    ];

    foreach ($emails as $mail) {
        if (!empty($mail)) {
            sendMail($mail, $subject, $body);
        }
    }

    header("Location: view_requests.php?msg=approved");
    exit();
}

/* REJECT REQUEST */
if ($action == "reject") {

    $update = $conn->prepare("
    UPDATE hostel_leaves
    SET status='Rejected',
        mess_cut=0
    WHERE id=?
    ");

    $update->bind_param("i", $leave_id);
    $update->execute();

    $subject = "Leave Rejected - Hostel Leave System";

    $body = "
    <h3>Leave Rejected</h3>
    <p><strong>Student:</strong> {$data['name']}</p>
    <p><strong>From:</strong> {$data['from_datetime']}</p>
    <p><strong>To:</strong> {$data['to_datetime']}</p>
    <p><strong>Status:</strong> Rejected</p>
    <p>Your leave request has been rejected by the warden.</p>
    ";

    $emails = [
        $data["email"],
        $data["parent_email"],
        $data["teacher_email"]
    ];

    foreach ($emails as $mail) {
        if (!empty($mail)) {
            sendMail($mail, $subject, $body);
        }
    }

    header("Location: view_requests.php?msg=rejected");
    exit();
}

/* INVALID ACTION */
echo "Invalid Action";
?>