<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit();
}
?>

<h2>Warden Dashboard</h2>
<a href="logout.php">Logout</a>

<?php
$sql = "SELECT hl.*, u.name, lt.type_name 
        FROM hostel_leaves hl
        JOIN users u ON hl.student_id = u.id
        JOIN leave_types lt ON hl.leave_type_id = lt.id
        ORDER BY hl.applied_at DESC";

$result = $conn->query($sql);
?>

<table border="1">
<tr>
    <th>Student</th>
    <th>Type</th>
    <th>From</th>
    <th>To</th>
    <th>Reason</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php while($row = $result->fetch_assoc()) { ?>
<tr>
    <td><?php echo $row['name']; ?></td>
    <td><?php echo $row['type_name']; ?></td>
    <td><?php echo $row['from_datetime']; ?></td>
    <td><?php echo $row['to_datetime']; ?></td>
    <td><?php echo $row['reason']; ?></td>
    <td><?php echo $row['status']; ?></td>
    <td>
        <a href="update_status.php?id=<?php echo $row['id']; ?>&status=Approved">Approve</a> |
        <a href="update_status.php?id=<?php echo $row['id']; ?>&status=Rejected">Reject</a>
    </td>
</tr>
<?php } ?>
</table>