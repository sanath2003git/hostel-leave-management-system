<?php
include("../includes/auth_check.php");

if ($_SESSION["role"] != "student") {
    echo "Access Denied";
    exit();
}

include("../config/db.php");

$user_id = $_SESSION["user_id"];

/* ===== REAL STATISTICS ===== */
$pending = 0;
$approved = 0;

$result1 = $conn->query("SELECT COUNT(*) as total FROM hostel_leaves WHERE student_id='$user_id' AND status='Pending'");
if($row = $result1->fetch_assoc()){
    $pending = $row['total'];
}

$result2 = $conn->query("SELECT COUNT(*) as total FROM hostel_leaves WHERE student_id='$user_id' AND status='Approved'");
if($row = $result2->fetch_assoc()){
    $approved = $row['total'];
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Dashboard</title>
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
    display:flex;
    justify-content:center;
    align-items:center;
    padding: 120px 60px 60px 60px;
    background: linear-gradient(
        135deg,
        #dcdde1 0%,
        #eceef2 40%,
        #d6d8de 100%
    );
}

body::before {
    content: "";
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 300px;
    background: linear-gradient(to bottom, rgba(0,0,0,0.08), transparent);
    pointer-events: none;
}

/* MAIN DASHBOARD CARD */
.dashboard{
    width:100%;
    max-width:1100px;
    padding:70px;
    border-radius:22px;
    background:#ffffff;
    box-shadow:
        0 30px 80px rgba(0,0,0,0.12),
        0 10px 25px rgba(0,0,0,0.08);
}

h1{
    font-size:36px;
    margin-bottom:12px;
    font-weight:600;
}

.subtitle{
     color: #555;
    margin-bottom:50px;
    font-size:15px;
}

/* STATS */
.stats{
    display:grid;
    grid-template-columns: repeat(auto-fit,minmax(220px,1fr));
    gap:10px;
    margin-bottom:60px;
}

.stat-card {
    padding: 30px 40px;
    border-radius: 18px;
    background: #ffffff;
    position: relative;
    box-shadow:
        0 30px 80px rgba(0,0,0,0.12),
        0 10px 25px rgba(0,0,0,0.08);
    transition: 0.25s ease;
}

.stat-card:hover{
    transform:translateY(-4px);
}

.stat-card h2 {
    font-size: 44px;
    font-weight: 700;
     margin-bottom: 6px;
    letter-spacing: -1px;
}

.stat-card p {
    color: #888;
    font-size: 14px;
    margin-top: 6px;
}

.stat-card::before {
    content: "";
    position: absolute;
    left: 0;
    top: 25%;
    height: 50%;
    width: 4px;
    background: #111;
    border-radius: 4px;
}

/* ACTION CARDS */
.actions{
    display:grid;
    grid-template-columns: repeat(auto-fit,minmax(260px,1fr));
    gap:30px;
}

.action-card{
    padding:35px;
    border-radius:18px;
    background:#111;
    color:#fff;
    box-shadow:0 20px 50px rgba(0,0,0,0.25);
    transition:0.25s ease;
}

.action-card:hover{
    transform:translateY(-5px);
}

.action-card p{
    font-size:14px;
    color:#ccc;
    margin:18px 0;
}

.action-card a {
    display: inline-block;
    padding: 10px 18px;
    background: transparent;
    color: #fff;
    text-decoration: none;
    border: 1px solid #444;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    transition: 0.25s ease;
}

.action-card a:hover {
    background: #fff;
    color: #111;
    border-color: #fff;
}
.logout{
    
    text-align:right;
}

.logout a{
    text-decoration:none;
    color:#111;
    font-weight:500;
}

.topbar{
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 70px;
    background: #f6f6f8;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 60px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    z-index: 100;
}

.logo{
    font-weight: 600;
    font-size: 16px;
}

.user {
    color: #888;
    font-weight: 500;
}

.divider {
    border: none;
    height: 1px;
    background: #eee;
    margin: 40px 0;
}

.section-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 20px;
    color: #444;
}

.topbar a {
    margin-left: 15px;
    font-weight: 500;
    color: #333;
    text-decoration: none;
}

</style>
</head>

<body>

<div class="topbar">
    <div class="logo">Hostel Leave System</div>
    <div class="user">
        <?php echo $_SESSION["username"]; ?>
        <div class="logout">
        <a href="../auth/logout.php">Logout</a>
    </div>
    </div>
</div>

<div class="dashboard">

    <h1>Welcome, <?php echo $_SESSION["username"]; ?></h1>
    <div class="subtitle">
        Manage your hostel leave efficiently with a modern digital system.
    </div>

    <hr class="divider">
<h3 class="section-title">Overview</h3>
    <!-- STATISTICS -->
    <div class="stats">
        <div class="stat-card">
            <h2><?php echo $pending; ?></h2>
            <p>Pending Leaves</p>
        </div>

        <div class="stat-card">
            <h2><?php echo $approved; ?></h2>
            <p>Approved Leaves</p>
        </div>
    </div>

    <!-- ACTIONS -->
    <div class="actions">
        <div class="action-card">
            <h3>Apply Leave</h3>
            <p>Submit a new leave request online.</p>
            <a href="apply_leave.php">Apply Now</a>
        </div>

        <div class="action-card">
            <h3>Leave Status</h3>
            <p>Track approval progress and remarks.</p>
            <a href="leave_status.php">View Status</a>
        </div>

        <div class="action-card">
            <h3>Leave History</h3>
            <p>Review your complete leave records.</p>
            <a href="leave_history.php">View History</a>
        </div>
    </div>

</div>


</body>
</html>