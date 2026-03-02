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

    $stmt = $conn->prepare("INSERT INTO hostel_leaves 
        (student_id, leave_type, from_datetime, to_datetime, reason)
        VALUES (?, ?, ?, ?, ?)");

    $stmt->bind_param("issss", $student_id, $leave_type, $from_date, $to_date, $reason);

    if ($stmt->execute()) {
        $success = "Leave Applied Successfully!";
    } else {
        $error = "Error applying leave.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Apply Leave</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    min-height:100vh;
    background: linear-gradient(135deg,#dcdde1 0%,#eceef2 40%,#d6d8de 100%);
    padding:120px 60px 60px 60px;
}

/* Topbar */
.topbar{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:70px;
    background:#f6f6f8;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:0 60px;
    box-shadow:0 2px 12px rgba(0,0,0,0.06);
}

.topbar a{
    text-decoration:none;
    color:#333;
    font-weight:500;
}

/* Container */
.dashboard{
    max-width:900px;
    margin:auto;
    padding:60px;
    background:#fff;
    border-radius:22px;
    box-shadow:
        0 30px 80px rgba(0,0,0,0.12),
        0 10px 25px rgba(0,0,0,0.08);
}

h2{
    font-size:28px;
    margin-bottom:30px;
}

/* Alerts */
.alert{
    padding:12px 15px;
    border-radius:10px;
    margin-bottom:20px;
    font-size:14px;
}

.success{
    background:#e6f4ea;
    color:#1e7e34;
}

.error{
    background:#fdecea;
    color:#c82333;
}

/* Form */
.input-group{
    margin-bottom:20px;
}

.input-group label{
    display:block;
    margin-bottom:6px;
    font-size:14px;
    color:#555;
}

.input-group input,
.input-group select,
.input-group textarea{
    width:100%;
    padding:12px 14px;
    border-radius:10px;
    border:1px solid #e5e5e7;
    font-size:14px;
}

.input-group textarea{
    resize:none;
    height:100px;
}

/* Buttons */
.btn-primary{
    background:#111;
    color:#fff;
    padding:12px 20px;
    border-radius:10px;
    border:none;
    cursor:pointer;
    font-weight:500;
}

.btn-primary:hover{
    background:#000;
}

.back-link{
    display:inline-block;
    margin-top:20px;
    text-decoration:none;
    color:#333;
}
</style>
</head>

<body>

<div class="topbar">
    <div>Hostel Leave System</div>
    <div>
        <?php echo $_SESSION["username"]; ?> |
        <a href="../auth/logout.php">Logout</a>
    </div>
</div>

<div class="dashboard">

    <h2>Apply Leave</h2>

    <?php if(isset($success)): ?>
        <div class="alert success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if(isset($error)): ?>
        <div class="alert error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">

        <div class="input-group">
            <label>Leave Type</label>
            <select name="leave_type" required>
                <option value="Day">Day</option>
                <option value="Night">Night</option>
                <option value="Home">Home</option>
            </select>
        </div>

        <div class="input-group">
            <label>From</label>
            <input type="datetime-local" name="from_date" required>
        </div>

        <div class="input-group">
            <label>To</label>
            <input type="datetime-local" name="to_date" required>
        </div>

        <div class="input-group">
            <label>Reason</label>
            <textarea name="reason" required></textarea>
        </div>

        <button type="submit" class="btn-primary">Submit Leave</button>

    </form>

    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>

</div>

</body>
</html>