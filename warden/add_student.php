<?php
include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}

$message = "";

/* Get student role ID */
$role_stmt = $conn->prepare("SELECT id FROM roles WHERE role_name = 'student' LIMIT 1");
$role_stmt->execute();
$role_result = $role_stmt->get_result();
$role_data = $role_result->fetch_assoc();
$student_role_id = $role_data["id"];
$role_stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $username = trim($_POST["username"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $email = trim($_POST["email"]);
    $parent_email = trim($_POST["parent_email"]);
    $teacher_email = trim($_POST["teacher_email"]);

    $register_number = trim($_POST["register_number"]);
    $department = trim($_POST["department"]);
    $year = intval($_POST["year"]);
    $room_number = trim($_POST["room_number"]);

    /* Check if username exists */
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $message = "Username already exists!";
    } else {

        /* Insert into users table */
        $insert_stmt = $conn->prepare("
            INSERT INTO users 
            (role_id, name, username, password, email, parent_email, teacher_email)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $insert_stmt->bind_param(
            "issssss",
            $student_role_id,
            $name,
            $username,
            $password,
            $email,
            $parent_email,
            $teacher_email
        );

        if ($insert_stmt->execute()) {

            $user_id = $conn->insert_id;

            /* Insert into student_profiles */
            $profile_stmt = $conn->prepare("
                INSERT INTO student_profiles
                (user_id, register_number, department, year, room_number)
                VALUES (?, ?, ?, ?, ?)
            ");

            $profile_stmt->bind_param(
                "issis",
                $user_id,
                $register_number,
                $department,
                $year,
                $room_number
            );

            $profile_stmt->execute();
            $profile_stmt->close();

            $message = "Student added successfully!";
        } else {
            $message = "Error adding student!";
        }

        $insert_stmt->close();
    }

    $check_stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
    <style>
        body {
            font-family: Arial;
            padding: 40px;
            background: #f4f4f4;
        }

        input {
            padding: 8px;
            width: 300px;
            margin-bottom: 10px;
        }

        button {
            padding: 8px 15px;
            background: #333;
            color: white;
            border: none;
        }

        .msg {
            margin: 15px 0;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>Add New Student</h2>

<?php if ($message != ""): ?>
    <div class="msg"><?php echo $message; ?></div>
<?php endif; ?>

<form method="POST">

    <label>Name:</label><br>
    <input type="text" name="name" required><br>

    <label>Username:</label><br>
    <input type="text" name="username" required><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br>

    <label>Student Email:</label><br>
    <input type="email" name="email"><br>

    <label>Parent Email:</label><br>
    <input type="email" name="parent_email"><br>

    <label>Teacher Email:</label><br>
    <input type="email" name="teacher_email"><br>

    <label>Register Number:</label><br>
    <input type="text" name="register_number"><br>

    <label>Department:</label><br>
    <input type="text" name="department"><br>

    <label>Year:</label><br>
    <input type="number" name="year"><br>

    <label>Room Number:</label><br>
    <input type="text" name="room_number"><br><br>

    <button type="submit">Add Student</button>

</form>

<br>
<a href="dashboard.php">Back</a>

</body>
</html>