<?php
include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "student") {
    echo "Access Denied";
    exit();
}

$success = "";
$error = "";

/* Fetch leave types */
$leaveTypes = $conn->query("SELECT * FROM leave_types");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $student_id = $_SESSION["user_id"];
    $leave_type_id = $_POST["leave_type_id"];
    $from_date = $_POST["from_date"];
    $to_date = $_POST["to_date"];
    $reason = trim($_POST["reason"]);

    if ($from_date > $to_date) {
        $error = "From date cannot be greater than To date.";
    } else {

        $stmt = $conn->prepare("INSERT INTO hostel_leaves 
            (student_id, leave_type_id, from_datetime, to_datetime, reason)
            VALUES (?, ?, ?, ?, ?)");

        $stmt->bind_param("iisss", $student_id, $leave_type_id, $from_date, $to_date, $reason);

        if ($stmt->execute()) {
            $success = "Leave Applied Successfully!";
        } else {
            $error = "Error applying leave.";
        }

        $stmt->close();
    }
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

/* ===== TOPBAR (MATCHES DASHBOARD) ===== */

.topbar{
position:fixed;
top:0;
left:0;
width:100%;
height:70px;

background:#111;
color:#fff;

display:flex;
justify-content:space-between;
align-items:center;

padding:0 60px;

border-bottom:1px solid #dcdcdc;

z-index:1000;
}

.logo{
font-size:18px;
font-weight:600;
}

.nav-right{
display:flex;
align-items:center;
gap:25px;
}

.nav-right a{
color:#fff;
text-decoration:none;
font-weight:500;
}

.nav-right a:hover{
opacity:0.8;
}

/* ===== MAIN CONTAINER ===== */

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

/* ALERT MESSAGES */

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

/* FORM */

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

/* BUTTON */

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

/* BACK LINK */

.back-btn{
display:inline-block;
margin-top:20px;
padding:10px 14px;
border:1px solid #111;
border-radius:8px;
text-decoration:none;
color:#fff;
background:#111;
}

.back-btn:hover{
background:#444;
color:#fff;
}

</style>
</head>

<body>

<!-- ===== TOPBAR ===== -->

<div class="topbar">

<div class="logo">Hostel Leave System</div>

<div class="nav-right">
<a href="profile.php"><?php echo $_SESSION["username"]; ?></a>
<a href="../auth/logout.php">Logout</a>
</div>

</div>

<!-- ===== APPLY LEAVE FORM ===== -->

<div class="dashboard">

<h2>Apply Leave</h2>

<?php if($success): ?>
<div class="alert success"><?php echo $success; ?></div>
<?php endif; ?>

<?php if($error): ?>
<div class="alert error"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST">

<div class="input-group">
<label>Leave Type</label>
<select name="leave_type_id" required>
<option value="">Select Leave Type</option>

<?php while($row = $leaveTypes->fetch_assoc()): ?>

<option value="<?php echo $row['id']; ?>">
<?php echo $row['type_name']; ?>
</option>

<?php endwhile; ?>

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

<a href="dashboard.php" class="back-btn">← Back to Dashboard</a>

</div>

</body>
</html>