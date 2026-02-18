<?php
include("../includes/auth_check.php");

if ($_SESSION["role"] != "student") {
    echo "Access Denied";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $_SESSION["leaves"][] = [
        "student" => $_SESSION["username"],
        "leave_type" => $_POST["leave_type"],
        "from_date" => $_POST["from_date"],
        "to_date" => $_POST["to_date"],
        "reason" => $_POST["reason"],
        "status" => "Pending"
    ];

    echo "<p style='color:green;'>Leave Applied Successfully!</p>";
}
?>

<h2>Apply Leave</h2>

<form method="POST">
    Leave Type:
    <select name="leave_type">
        <option>Day</option>
        <option>Night</option>
        <option>Home</option>
    </select><br><br>

    From: <input type="date" name="from_date" required><br><br>
    To: <input type="date" name="to_date" required><br><br>
    Reason: <textarea name="reason" required></textarea><br><br>

    <button type="submit">Submit</button>
</form>

<br>
<a href="dashboard.php">Back</a>