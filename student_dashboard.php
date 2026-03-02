<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
</head>
<body>

<h2>Student Dashboard</h2>
<a href="logout.php">Logout</a>

<h3>Apply Leave</h3>

<form action="apply_leave.php" method="POST">

    Leave Type:
    <select name="leave_type" required>
        <option value="">-- Select Leave Type --</option>
        <?php
        $types = $conn->query("SELECT * FROM leave_types");
        while ($type = $types->fetch_assoc()) {
            echo "<option value='" . $type['id'] . "'>" . $type['type_name'] . "</option>";
        }
        ?>
    </select>
    <br><br>

    From:
    <input type="datetime-local" name="from_datetime" required>
    <br><br>

    To:
    <input type="datetime-local" name="to_datetime" required>
    <br><br>

    Reason:
    <input type="text" name="reason" required>
    <br><br>

    <button type="submit">Apply</button>
</form>

<h3>Your Leaves</h3>

<?php
$student_id = $_SESSION['user_id'];

$sql = "SELECT hl.*, lt.type_name 
        FROM hostel_leaves hl
        JOIN leave_types lt ON hl.leave_type_id = lt.id
        WHERE hl.student_id='$student_id'
        ORDER BY hl.applied_at DESC";

$result = $conn->query($sql);
?>

<table border="1">
<tr>
    <th>Type</th>
    <th>From</th>
    <th>To</th>
    <th>Reason</th>
    <th>Status</th>
</tr>

<?php while($row = $result->fetch_assoc()) { ?>
<tr>
    <td><?php echo $row['type_name']; ?></td>
    <td><?php echo $row['from_datetime']; ?></td>
    <td><?php echo $row['to_datetime']; ?></td>
    <td><?php echo $row['reason']; ?></td>
    <td><?php echo $row['status']; ?></td>
</tr>
<?php } ?>

</table>

</body>
</html>