<?php
include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}

$stmt = $conn->prepare("
SELECT users.name,
       student_profiles.room_number,
       hostel_leaves.from_datetime,
       hostel_leaves.to_datetime
FROM hostel_leaves
JOIN users ON hostel_leaves.student_id = users.id
JOIN student_profiles ON users.id = student_profiles.user_id
WHERE hostel_leaves.status = 'Approved'
AND hostel_leaves.returned_at IS NULL
AND NOW() > hostel_leaves.to_datetime
");

$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Late Students</h2>

<table border="1" cellpadding="10">
<tr>
<th>Name</th>
<th>Room</th>
<th>Leave From</th>
<th>Leave To</th>
</tr>

<?php while($row = $result->fetch_assoc()) { ?>

<tr>
<td><?php echo $row["name"]; ?></td>
<td><?php echo $row["room_number"]; ?></td>
<td><?php echo $row["from_datetime"]; ?></td>
<td><?php echo $row["to_datetime"]; ?></td>
</tr>

<?php } ?>

</table>