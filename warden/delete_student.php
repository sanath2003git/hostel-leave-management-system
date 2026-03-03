<?php
include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}

if (!isset($_GET["id"])) {
    echo "Invalid Request";
    exit();
}

$user_id = intval($_GET["id"]);

// Check if user exists and is student
$check = $conn->prepare("
    SELECT users.id
    FROM users
    JOIN roles ON users.role_id = roles.id
    WHERE users.id = ? AND roles.role_name = 'student'
");
$check->bind_param("i", $user_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows == 0) {
    echo "Student not found.";
    exit();
}

// Delete student (profile auto-deletes because of ON DELETE CASCADE)
$delete = $conn->prepare("DELETE FROM users WHERE id = ?");
$delete->bind_param("i", $user_id);
$delete->execute();

header("Location: view_students.php");
exit();
?>