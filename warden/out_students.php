<?php
include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}

$stmt = $conn->prepare("
    SELECT users.name,
           student_profiles.department,
           student_profiles.room_number,
           hostel_leaves.from_datetime,
           hostel_leaves.to_datetime
    FROM hostel_leaves
    JOIN users ON hostel_leaves.student_id = users.id
    JOIN student_profiles ON users.id = student_profiles.user_id
    WHERE hostel_leaves.status = 'Approved'
      AND NOW() BETWEEN hostel_leaves.from_datetime AND hostel_leaves.to_datetime
    ORDER BY hostel_leaves.from_datetime ASC
");

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Students Currently Outside</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            background: #f4f4f4;
        }

        h2 {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        th {
            background: #333;
            color: white;
        }

        tr:hover {
            background: #f9f9f9;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            background: #555;
            color: white;
            padding: 8px 12px;
            text-decoration: none;
        }

        .status-box {
            padding: 15px;
            background: white;
            border-radius: 6px;
        }
    </style>
</head>
<body>

<h2>Students Currently Outside Hostel</h2>

<?php if ($result->num_rows == 0): ?>

<div class="status-box">
    No students are currently outside.
</div>

<?php else: ?>

<table>
    <tr>
        <th>Name</th>
        <th>Department</th>
        <th>Room</th>
        <th>From</th>
        <th>To</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($row["name"]); ?></td>
        <td><?php echo htmlspecialchars($row["department"]); ?></td>
        <td><?php echo htmlspecialchars($row["room_number"]); ?></td>
        <td><?php echo htmlspecialchars($row["from_datetime"]); ?></td>
        <td><?php echo htmlspecialchars($row["to_datetime"]); ?></td>
    </tr>
    <?php endwhile; ?>

</table>

<?php endif; ?>

<a class="back-btn" href="dashboard.php">Back to Dashboard</a>

</body>
</html>