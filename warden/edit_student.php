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
$message = "";

// Fetch existing student data
$stmt = $conn->prepare("
    SELECT users.*, student_profiles.*
    FROM users
    JOIN student_profiles ON users.id = student_profiles.user_id
    WHERE users.id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    echo "Student not found.";
    exit();
}

// Update student
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $parent_email = trim($_POST["parent_email"]);
    $teacher_email = trim($_POST["teacher_email"]);

    $register_number = trim($_POST["register_number"]);
    $department = trim($_POST["department"]);
    $year = intval($_POST["year"]);
    $room_number = trim($_POST["room_number"]);

    // Update users table
    $update_user = $conn->prepare("
        UPDATE users
        SET name = ?, email = ?, parent_email = ?, teacher_email = ?
        WHERE id = ?
    ");
    $update_user->bind_param(
        "ssssi",
        $name,
        $email,
        $parent_email,
        $teacher_email,
        $user_id
    );
    $update_user->execute();

    // Update student_profiles table
    $update_profile = $conn->prepare("
        UPDATE student_profiles
        SET register_number = ?, department = ?, year = ?, room_number = ?
        WHERE user_id = ?
    ");
    $update_profile->bind_param(
        "ssisi",
        $register_number,
        $department,
        $year,
        $room_number,
        $user_id
    );
    $update_profile->execute();

    $message = "Student updated successfully.";

    // Refresh data
    header("Location: view_students.php");
    exit();
}
?>

<h2>Edit Student</h2>

<form method="POST">

Name:<br>
<input type="text" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required><br><br>

Register Number:<br>
<input type="text" name="register_number" value="<?php echo htmlspecialchars($student['register_number']); ?>" required><br><br>

Department:<br>
<input type="text" name="department" value="<?php echo htmlspecialchars($student['department']); ?>" required><br><br>

Year:<br>
<input type="number" name="year" value="<?php echo htmlspecialchars($student['year']); ?>" required><br><br>

Room Number:<br>
<input type="text" name="room_number" value="<?php echo htmlspecialchars($student['room_number']); ?>" required><br><br>

Student Email:<br>
<input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>"><br><br>

Parent Email:<br>
<input type="email" name="parent_email" value="<?php echo htmlspecialchars($student['parent_email']); ?>"><br><br>

Teacher Email:<br>
<input type="email" name="teacher_email" value="<?php echo htmlspecialchars($student['teacher_email']); ?>"><br><br>

<button type="submit">Update Student</button>

</form>

<br>
<a href="view_students.php">Back</a>