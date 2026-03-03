<?php
include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}

$stmt = $conn->prepare("
    SELECT users.id, users.name, users.username, users.email,
           student_profiles.register_number,
           student_profiles.department,
           student_profiles.year,
           student_profiles.room_number
    FROM users
    JOIN roles ON users.role_id = roles.id
    JOIN student_profiles ON users.id = student_profiles.user_id
    WHERE roles.role_name = 'student'
    ORDER BY users.name ASC
");

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Students</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            background-color: #f4f4f4;
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
            text-align: left;
        }

        th {
            background-color: #333;
            color: white;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        a {
            text-decoration: none;
            padding: 5px 8px;
            border-radius: 4px;
            font-size: 13px;
        }

        .edit-btn {
            background: #007bff;
            color: white;
        }

        .delete-btn {
            background: #dc3545;
            color: white;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            background: #555;
            color: white;
            padding: 8px 12px;
        }
    </style>
</head>
<body>

<h2>All Students</h2>

<table>
    <tr>
        <th>Name</th>
        <th>Register No</th>
        <th>Department</th>
        <th>Year</th>
        <th>Room</th>
        <th>Username</th>
        <th>Email</th>
        <th>Action</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($row["name"]); ?></td>
        <td><?php echo htmlspecialchars($row["register_number"]); ?></td>
        <td><?php echo htmlspecialchars($row["department"]); ?></td>
        <td><?php echo htmlspecialchars($row["year"]); ?></td>
        <td><?php echo htmlspecialchars($row["room_number"]); ?></td>
        <td><?php echo htmlspecialchars($row["username"]); ?></td>
        <td><?php echo htmlspecialchars($row["email"]); ?></td>
        <td>
            <a class="edit-btn" href="edit_student.php?id=<?php echo $row['id']; ?>">Edit</a>
            <a class="delete-btn" 
               href="delete_student.php?id=<?php echo $row['id']; ?>"
               onclick="return confirm('Are you sure you want to delete this student?');">
               Delete
            </a>
        </td>
    </tr>
    <?php endwhile; ?>

</table>

<a class="back-btn" href="dashboard.php">Back to Dashboard</a>

</body>
</html>