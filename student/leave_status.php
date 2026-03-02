<?php
include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "student") {
    echo "Access Denied";
    exit();
}

$student_id = $_SESSION["user_id"];

$stmt = $conn->prepare("SELECT * FROM hostel_leaves 
                        WHERE student_id = ?
                        ORDER BY applied_at DESC
                        LIMIT 1");

$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>Leave Status</title>
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
    margin-bottom:40px;
}

/* Status Card */
.status-card{
    padding:35px;
    border-radius:18px;
    background:#ffffff;
    border:1px solid #eee;
    box-shadow:
        0 12px 35px rgba(0,0,0,0.06);
}

.status-row{
    margin-bottom:15px;
    font-size:15px;
}

.status-row strong{
    font-weight:600;
}

/* Status Badge */
.badge{
    display:inline-block;
    padding:6px 12px;
    border-radius:20px;
    font-size:13px;
    font-weight:600;
}

.pending{
    background:#fff3cd;
    color:#856404;
}

.approved{
    background:#e6f4ea;
    color:#1e7e34;
}

.rejected{
    background:#fdecea;
    color:#c82333;
}

.back-link{
    display:inline-block;
    margin-top:30px;
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

<h2>Latest Leave Status</h2>

<?php if ($result->num_rows == 0): ?>
    <div class="status-card">
        No leave applications found.
    </div>
<?php else: 
    $row = $result->fetch_assoc();
    $status = strtolower($row["status"]);
?>

<div class="status-card">

    <div class="status-row">
        <strong>Leave Type:</strong> <?php echo htmlspecialchars($row["leave_type"]); ?>
    </div>

    <div class="status-row">
        <strong>From:</strong> <?php echo htmlspecialchars($row["from_datetime"]); ?>
    </div>

    <div class="status-row">
        <strong>To:</strong> <?php echo htmlspecialchars($row["to_datetime"]); ?>
    </div>

    <div class="status-row">
        <strong>Reason:</strong> <?php echo htmlspecialchars($row["reason"]); ?>
    </div>

    <div class="status-row">
        <strong>Status:</strong> 
        <span class="badge <?php echo $status; ?>">
            <?php echo htmlspecialchars($row["status"]); ?>
        </span>
    </div>

</div>

<?php endif; ?>

<a href="dashboard.php" class="back-link">← Back to Dashboard</a>

</div>

</body>
</html>