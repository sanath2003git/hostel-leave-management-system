<?php
include("../includes/auth_check.php");
include("../config/db.php");

// Fetch students
$query = "SELECT * FROM users WHERE role_id = 1";
$result = mysqli_query($conn, $query);

// Handle form submit
if (isset($_POST['submit'])) {

    $date = date("Y-m-d");
    $warden_id = $_SESSION['user_id'];

    foreach ($_POST['attendance'] as $user_id => $status) {

        // 🔍 Check approved leave
        $leaveQuery = "
        SELECT * FROM hostel_leaves 
        WHERE student_id = ? 
        AND status = 'Approved'
        AND DATE(from_datetime) <= ?
        AND DATE(to_datetime) >= ?
        ";

        $stmt = $conn->prepare($leaveQuery);
        $stmt->bind_param("iss", $user_id, $date, $date);
        $stmt->execute();
        $leaveResult = $stmt->get_result();

        $hasLeave = $leaveResult->num_rows > 0;

        // 🧠 Logic
        if ($status == "Absent") {

            if ($hasLeave) {
                $finalStatus = "Leave";
                $remark = "Normal";
            } else {
                $finalStatus = "Absent";
                $remark = "Unauthorized";
            }

        } else {
            $finalStatus = "Present";
            $remark = "Normal";
        }

        // 💾 Save attendance
        $stmt = $conn->prepare("
            INSERT INTO attendance (user_id, date, status, remark, marked_by)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            status = VALUES(status),
            remark = VALUES(remark)
        ");

        $stmt->bind_param("isssi", $user_id, $date, $finalStatus, $remark, $warden_id);
        $stmt->execute();
    }

    echo "<p style='color:green;'>Attendance saved successfully!</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Attendance</title>
</head>
<body>

<h2>Mark Attendance</h2>

<form method="POST">

<table border="1" cellpadding="10">
<tr>
    <th>Name</th>
    <th>Status</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?php echo $row['name']; ?></td>
    <td>
        <select name="attendance[<?php echo $row['id']; ?>]">
            <option value="Present">Present</option>
            <option value="Absent">Absent</option>
        </select>
    </td>
</tr>
<?php } ?>

</table>

<br>
<button type="submit" name="submit">Save Attendance</button>

</form>

</body>
</html>