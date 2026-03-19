<?php

include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}

/* MAIN REQUEST QUERY */

$query = "

SELECT hostel_leaves.*, users.name, leave_types.type_name
FROM hostel_leaves
JOIN users 
    ON hostel_leaves.student_id = users.id
JOIN leave_types 
    ON hostel_leaves.leave_type_id = leave_types.id
ORDER BY hostel_leaves.applied_at DESC

";

$result = mysqli_query($conn, $query);

/* ===== STATISTICS ===== */

$total = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM hostel_leaves"))['total'];

$pending = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM hostel_leaves WHERE status='Pending'"))['total'];

$approved = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM hostel_leaves WHERE status='Approved'"))['total'];

$returned = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM hostel_leaves WHERE returned_at IS NOT NULL"))['total'];

?>

<!DOCTYPE html>
<html>

<head>

<title>Leave Requests</title>

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
background: linear-gradient(135deg,#e8eaef 0%,#f4f5f8 40%,#e6e8ed 100%);
padding:120px 60px 60px 60px;
}

/* TOPBAR */

.topbar{
position:fixed;
top:0;
left:0;
width:100%;
height:70px;
background:#111;

display:flex;
justify-content:space-between;
align-items:center;

padding:0 60px;
color:#fff;

box-shadow:0 6px 20px rgba(0,0,0,0.35);
border-bottom:1px solid #222;

z-index:1000;
}

.topbar a{
text-decoration:none;
color:#fff;
font-weight:500;
}

/* PAGE CONTAINER */

.container{
max-width:1100px;
margin:auto;
padding:60px;
background:#fff;
border-radius:22px;

box-shadow:
0 40px 90px rgba(0,0,0,0.12),
0 15px 35px rgba(0,0,0,0.08);
}

h1{
margin-bottom:25px;
}

/* ===== STATISTICS ===== */

.stats{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
gap:20px;
margin-bottom:35px;
}

.stat-card{
background:#fff;
border-radius:16px;
padding:22px;
border:1px solid #eee;

box-shadow:0 8px 20px rgba(0,0,0,0.05);
}

.stat-card p{
font-size:13px;
color:#666;
margin-bottom:6px;
}

.stat-card h2{
font-size:28px;
}

/* REQUEST CARD */

.card{
background:#fafafa;
padding:28px;
margin-bottom:25px;
border-radius:16px;
border:1px solid #eee;

box-shadow:0 8px 20px rgba(0,0,0,0.06);
}

.card-header{
display:flex;
justify-content:space-between;
margin-bottom:18px;
}

.student{
font-size:18px;
font-weight:600;
}

.status{
font-weight:600;
font-size:13px;
padding:6px 12px;
border-radius:20px;
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

.row{
margin-bottom:8px;
font-size:14px;
color:#555;
}

/* ACTION BUTTONS */

.actions{
margin-top:18px;
}

.actions a{
text-decoration:none;
padding:9px 16px;
border-radius:8px;
font-size:13px;
margin-right:10px;
}

.approve{
background:#111;
color:white;
}

.reject{
background:#e74c3c;
color:white;
}

.return{
background:#2c7be5;
color:white;
}

.actions a:hover{
opacity:0.85;
}

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

.logout-btn{
padding:8px 16px;
border-radius:8px;
background:#111;
color:1#fff;
text-decoration:none;
font-size:14px;
font-weight:500;
margin-left:12px;
transition:0.2s;
}

.logout-btn:hover{
background: rgba(255, 255, 238, 0.15);
}

.logo{
font-size:18px;
font-weight:600;
letter-spacing:0.5px;
}

</style>

</head>

<body>

<div class="topbar">

<div class="logo">Hostel Leave System</div>

<div>
<?php echo $_SESSION["username"]; ?>
<a class="logout-btn" href="../auth/logout.php">Logout</a>
</div>

</div>


<div class="container">

<h1>Leave Requests</h1>


<!-- ===== STATISTICS ROW ===== -->

<div class="stats">

<div class="stat-card">
<p>Total Requests</p>
<h2><?php echo $total; ?></h2>
</div>

<div class="stat-card">
<p>Pending</p>
<h2><?php echo $pending; ?></h2>
</div>

<div class="stat-card">
<p>Approved</p>
<h2><?php echo $approved; ?></h2>
</div>

<div class="stat-card">
<p>Returned</p>
<h2><?php echo $returned; ?></h2>
</div>

</div>


<?php

if (mysqli_num_rows($result) == 0) {

    echo "No leave requests.";

} else {

while ($row = mysqli_fetch_assoc($result)) {

$status = strtolower($row["status"]);

?>

<div class="card">

<div class="card-header">

<div class="student">
<?php echo htmlspecialchars($row["name"]); ?>
</div>

<div class="status <?php echo $status; ?>">
<?php echo htmlspecialchars($row["status"]); ?>
</div>

</div>

<div class="row">
<strong>Leave Type:</strong> <?php echo htmlspecialchars($row["type_name"]); ?>
</div>

<div class="row">
<strong>From:</strong> <?php echo htmlspecialchars($row["from_datetime"]); ?>
</div>

<div class="row">
<strong>To:</strong> <?php echo htmlspecialchars($row["to_datetime"]); ?>
</div>

<div class="row">
<strong>Reason:</strong> <?php echo htmlspecialchars($row["reason"]); ?>
</div>

<?php if ($row["returned_at"] != NULL) { ?>

<div class="row">
<strong>Returned At:</strong> <?php echo htmlspecialchars($row["returned_at"]); ?>
</div>

<?php } ?>

<div class="actions">

<?php

if ($row["status"] == "Pending") {

echo "<a class='approve' href='approve_leave.php?id=".$row["id"]."&action=approve'>Approve</a>";

echo "<a class='reject' href='approve_leave.php?id=".$row["id"]."&action=reject'>Reject</a>";

}

if ($row["status"] == "Approved" && $row["returned_at"] == NULL) {

echo "<a class='return' href='mark_returned.php?id=".$row["id"]."'>Mark Returned</a>";

}

?>

</div>

</div>

<?php
}
}
?>

<a class="back-btn" href="dashboard.php">← Back to Dashboard</a>

</div>

</body>
</html>