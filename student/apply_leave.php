<?php
include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "student") {
    echo "Access Denied";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $student_id = $_SESSION["user_id"];
    $leave_type = $_POST["leave_type"];
    $from_date = $_POST["from_date"];
    $to_date = $_POST["to_date"];
    $reason = $_POST["reason"];

    $query = "INSERT INTO hostel_leaves 
              (student_id, leave_type, from_datetime, to_datetime, reason)
              VALUES 
              ('$student_id', '$leave_type', '$from_date', '$to_date', '$reason')";

    if (mysqli_query($conn, $query)) {
        $success = "Leave Applied Successfully!";
    } else {
        $error = "Error applying leave.";
    }
}
?>

<h2>Apply Leave</h2>

<?php
if(isset($success)) echo "<p style='color:green;'>$success</p>";
if(isset($error)) echo "<p style='color:red;'>$error</p>";
?>

<form method="POST">
    Leave Type:
    <select name="leave_type">
        <option value="Day">Day</option>
        <option value="Night">Night</option>
        <option value="Home">Home</option>
    </select><br><br>

    From: <input type="datetime-local" name="from_date" required><br><br>
    To: <input type="datetime-local" name="to_date" required><br><br>

    Reason:<br>
    <textarea name="reason" required></textarea><br><br>

    <button type="submit">Submit</button>
</form>

<br>
<a href="dashboard.php">Back</a>