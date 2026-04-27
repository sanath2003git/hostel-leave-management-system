<?php
include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search != '') {
    $stmt = $conn->prepare("
        SELECT hostel_leaves.*, users.name, users.register_number
        FROM hostel_leaves
        JOIN users ON hostel_leaves.student_id = users.id
        WHERE users.name LIKE ? OR users.register_number LIKE ?
        ORDER BY hostel_leaves.id DESC
    ");
    $like = "%$search%";
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("
        SELECT hostel_leaves.*, users.name, users.register_number
        FROM hostel_leaves
        JOIN users ON hostel_leaves.student_id = users.id
        ORDER BY hostel_leaves.id DESC
    ");
}
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
background:linear-gradient(135deg,#e8eaef 0%,#f4f5f8 40%,#e6e8ed 100%);
padding:120px 50px 50px 50px;
}

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
padding:0 50px;
color:#fff;
z-index:1000;
box-shadow:0 6px 20px rgba(0,0,0,0.35);
}

.logo{
font-size:18px;
font-weight:600;
}

.logout-btn{
padding:8px 16px;
background:#222;
color:#fff;
text-decoration:none;
border-radius:8px;
margin-left:12px;
}

.logout-btn:hover{
background:#444;
}

.container{
max-width:1350px;
margin:auto;
background:#fff;
padding:50px;
border-radius:22px;
box-shadow:0 30px 70px rgba(0,0,0,0.10);
}

h1{
font-size:32px;
margin-bottom:10px;
}

.sub{
color:#777;
font-size:14px;
margin-bottom:25px;
}

.search-box{
margin-bottom:25px;
display:flex;
gap:10px;
}

.search-box input{
padding:12px;
width:300px;
border:1px solid #ddd;
border-radius:10px;
outline:none;
}

.search-box button{
padding:12px 18px;
background:#111;
color:#fff;
border:none;
border-radius:10px;
cursor:pointer;
}

.search-box button:hover{
background:#444;
}

table{
width:100%;
border-collapse:collapse;
}

th{
background:#111;
color:#fff;
padding:14px;
font-size:14px;
text-align:left;
}

td{
padding:14px;
border-bottom:1px solid #eee;
font-size:14px;
vertical-align:top;
}

tr:hover{
background:#f8f9fb;
}

.badge{
padding:6px 10px;
border-radius:30px;
font-size:12px;
font-weight:600;
display:inline-block;
}

.pending{
background:#eef4ff;
color:#2563eb;
}

.approved{
background:#eafaf1;
color:#27ae60;
}

.rejected{
background:#fff0f0;
color:#e74c3c;
}

.btn{
padding:8px 12px;
text-decoration:none;
border-radius:8px;
font-size:13px;
margin-right:5px;
display:inline-block;
margin-bottom:5px;
}

.approve-btn{
background:#27ae60;
color:#fff;
}

.reject-btn{
background:#e74c3c;
color:#fff;
}

.return-btn{
background:#111;
color:#fff;
}

.btn:hover{
opacity:0.85;
}

.back-btn{
display:inline-block;
margin-top:25px;
padding:12px 18px;
background:#111;
color:#fff;
text-decoration:none;
border-radius:8px;
}

.back-btn:hover{
background:#444;
}

.empty{
padding:18px;
background:#fafafa;
border:1px solid #eee;
border-radius:10px;
margin-top:15px;
}

</style>

<script>
function approveConfirm(){
return confirm("Approve this leave request?");
}

function rejectConfirm(){
return confirm("Reject this leave request?");
}

function returnConfirm(){
return confirm("Mark student as returned?");
}
</script>

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
<div class="sub">Review, approve, reject and manage student leave requests.</div>

<form method="GET" class="search-box">
<input type="text" name="search" placeholder="Search student name / register no" value="<?php echo htmlspecialchars($search); ?>">
<button type="submit">Search</button>
</form>

<?php if($result->num_rows > 0) { ?>

<table>

<tr>
<th>Student</th>
<th>Reg No</th>
<th>From</th>
<th>To</th>
<th>Reason</th>
<th>Status</th>
<th>Actions</th>
</tr>

<?php while($row = $result->fetch_assoc()) { ?>

<tr>

<td><?php echo htmlspecialchars($row["name"]); ?></td>

<td><?php echo htmlspecialchars($row["register_number"]); ?></td>

<td><?php echo htmlspecialchars($row["from_datetime"]); ?></td>

<td><?php echo htmlspecialchars($row["to_datetime"]); ?></td>

<td><?php echo htmlspecialchars($row["reason"]); ?></td>

<td>

<?php
$status = $row["status"];

if($status=="Pending"){
echo "<span class='badge pending'>Pending</span>";
}
elseif($status=="Approved"){
echo "<span class='badge approved'>Approved</span>";
}
else{
echo "<span class='badge rejected'>Rejected</span>";
}
?>

</td>

<td>

<?php if($row["status"]=="Pending") { ?>

<a class="btn approve-btn"
href="approve_leave.php?id=<?php echo $row['id']; ?>&action=approve"
onclick="return approveConfirm()">Approve</a>

<a class="btn reject-btn"
href="approve_leave.php?id=<?php echo $row['id']; ?>&action=reject"
onclick="return rejectConfirm()">Reject</a>

<?php } ?>

<?php if($row["status"]=="Approved" && $row["returned_at"]==NULL) { ?>

<a class="btn return-btn"
href="mark_returned.php?id=<?php echo $row['id']; ?>"
onclick="return returnConfirm()">Mark Returned</a>

<?php } ?>

</td>

</tr>

<?php } ?>

</table>

<?php } else { ?>

<div class="empty">No leave requests found.</div>

<?php } ?>

<a href="dashboard.php" class="back-btn">← Back to Dashboard</a>

</div>

</body>
</html>