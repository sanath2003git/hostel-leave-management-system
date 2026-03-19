<?php
include("../includes/auth_check.php");
include("../config/db.php");

$user_id = $_SESSION['user_id'];

$query = "
SELECT date, status, remark 
FROM attendance 
WHERE user_id = ? 
ORDER BY date DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Your Attendance</h2>

<table border="1">
<tr>
    <th>Date</th>
    <th>Status</th>
    <th>Remark</th>
</tr>

<?php while($row = $result->fetch_assoc()) { ?>
<tr>
    <td><?php echo $row['date']; ?></td>
    <td><?php echo $row['status']; ?></td>
    <td><?php echo $row['remark']; ?></td>
</tr>
<?php } ?>

</table>