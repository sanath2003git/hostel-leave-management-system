<?php
include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "student") {
    echo "Access Denied";
    exit();
}

$student_id = $_SESSION["user_id"];

/* JOIN leave_types */
$stmt = $conn->prepare("
    SELECT hostel_leaves.*, leave_types.type_name
    FROM hostel_leaves
    JOIN leave_types 
    ON hostel_leaves.leave_type_id = leave_types.id
    WHERE hostel_leaves.student_id = ?
    ORDER BY hostel_leaves.applied_at DESC
");

$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>Leave History</title>

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

/* ===== TOPBAR ===== */

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

border-bottom:1px solid #e5e5e5;

z-index:1000;
}

.topbar a{
color:#fff;
text-decoration:none;
font-weight:500;
margin-left:20px;
}

/* ===== CONTAINER ===== */

.dashboard{
max-width:1000px;
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

/* TABLE */

.table{
width:100%;
border-collapse:collapse;
}

.table th{
text-align:left;
padding:15px;
font-size:14px;
color:#555;
border-bottom:1px solid #eee;
}

.table td{
padding:18px 15px;
border-bottom:1px solid #f0f0f0;
font-size:14px;
}

.table tr:hover{
background:#fafafa;
}

/* STATUS BADGE */

.badge{
padding:6px 12px;
border-radius:20px;
font-size:12px;
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

<!-- TOPBAR -->

<div class="topbar">

<div>Hostel Leave System</div>

<div>
<?php echo $_SESSION["username"]; ?> |
<a href="../auth/logout.php">Logout</a>
</div>

</div>

<!-- PAGE CONTENT -->

<div class="dashboard">

<h2>Leave History</h2>

<?php if ($result->num_rows == 0): ?>

<p>No leave history found.</p>

<?php else: ?>

<table class="table">

<thead>
<tr>
<th>Type</th>
<th>From</th>
<th>To</th>
<th>Status</th>
<th>Remarks</th>
</tr>
</thead>

<tbody>

<?php while ($row = $result->fetch_assoc()): 
$status = strtolower($row["status"]);
?>

<tr>

<td><?php echo htmlspecialchars($row["type_name"]); ?></td>

<td><?php echo htmlspecialchars($row["from_datetime"]); ?></td>

<td><?php echo htmlspecialchars($row["to_datetime"]); ?></td>

<td>
<span class="badge <?php echo $status; ?>">
<?php echo htmlspecialchars($row["status"]); ?>
</span>
</td>

<td>
<?php echo !empty($row["remarks"]) 
? htmlspecialchars($row["remarks"]) 
: "-"; ?>
</td>

</tr>

<?php endwhile; ?>

</tbody>
</table>

<?php endif; ?>

<a href="dashboard.php" class="back-btn">← Back to Dashboard</a>

</div>

</body>
</html>