<?php
include("../includes/auth_check.php");
include("../config/db.php");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}

/* FETCH STUDENTS */
$query = "
SELECT users.id, users.name
FROM users
JOIN roles ON users.role_id = roles.id
WHERE roles.role_name='student'
ORDER BY users.name ASC
";

$result = mysqli_query($conn, $query);

/* SAVE ATTENDANCE */
if (isset($_POST['submit'])) {

    $date = date("Y-m-d");
    $warden_id = $_SESSION['user_id'];

    foreach ($_POST['attendance'] as $user_id => $status) {

        /* CHECK APPROVED LEAVE */
        $leaveQuery = "
        SELECT id
        FROM hostel_leaves
        WHERE student_id = ?
        AND status='Approved'
        AND DATE(from_datetime) <= ?
        AND DATE(to_datetime) >= ?
        AND returned_at IS NULL
        ";

        $stmt = $conn->prepare($leaveQuery);
        $stmt->bind_param("iss", $user_id, $date, $date);
        $stmt->execute();
        $leaveResult = $stmt->get_result();

        $hasLeave = $leaveResult->num_rows > 0;

        if ($status == "Absent") {

            if ($hasLeave) {
                $finalStatus = "Leave";
                $remark = "Normal";
            } else {
                $finalStatus = "Absent";
                $remark = "Unauthorized";
            }

        } else {

            $finalStatus = "Present";
            $remark = "Normal";
        }

        $stmt = $conn->prepare("
        INSERT INTO attendance(user_id,date,status,remark,marked_by)
        VALUES(?,?,?,?,?)
        ON DUPLICATE KEY UPDATE
        status=VALUES(status),
        remark=VALUES(remark),
        marked_by=VALUES(marked_by)
        ");

        $stmt->bind_param("isssi", $user_id, $date, $finalStatus, $remark, $warden_id);
        $stmt->execute();
    }

    $success = "Attendance saved successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Attendance</title>

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
max-width:1100px;
margin:auto;
background:#fff;
padding:50px;
border-radius:24px;
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

.success{
padding:14px;
background:#eafaf1;
color:#27ae60;
border-radius:10px;
margin-bottom:20px;
font-size:14px;
}

.actions{
margin-bottom:18px;
}

.small-btn{
padding:10px 14px;
background:#111;
color:#fff;
border:none;
border-radius:8px;
cursor:pointer;
margin-right:10px;
}

.small-btn:hover{
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
}

tr:hover{
background:#f8f9fb;
}

select{
padding:10px 12px;
border:1px solid #ddd;
border-radius:8px;
outline:none;
}

.save-btn{
margin-top:25px;
padding:12px 18px;
background:#111;
color:#fff;
border:none;
border-radius:8px;
cursor:pointer;
font-size:14px;
}

.save-btn:hover{
background:#444;
}

.back-btn{
display:inline-block;
margin-top:25px;
margin-left:10px;
padding:12px 18px;
background:#111;
color:#fff;
text-decoration:none;
border-radius:8px;
}

.back-btn:hover{
background:#444;
}

</style>

<script>
function markAllPresent(){
let selects = document.querySelectorAll("select");
selects.forEach(function(sel){
sel.value = "Present";
});
}

function markAllAbsent(){
let selects = document.querySelectorAll("select");
selects.forEach(function(sel){
sel.value = "Absent";
});
}
</script>

</head>

<body>

<div class="topbar">

<div>Hostel Leave System</div>

<div>
<?php echo $_SESSION["username"]; ?>
<a class="logout-btn" href="../auth/logout.php">Logout</a>
</div>

</div>

<div class="container">

<h1>Attendance</h1>
<div class="sub">Mark daily student attendance records.</div>

<?php if(isset($success)) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>

<div class="actions">
<button type="button" class="small-btn" onclick="markAllPresent()">Mark All Present</button>
<button type="button" class="small-btn" onclick="markAllAbsent()">Mark All Absent</button>
</div>

<form method="POST">

<table>

<tr>
<th>Student Name</th>
<th>Status</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>

<tr>

<td><?php echo htmlspecialchars($row['name']); ?></td>

<td>
<select name="attendance[<?php echo $row['id']; ?>]">
<option value="Present">Present</option>
<option value="Absent">Absent</option>
</select>
</td>

</tr>

<?php } ?>

</table>

<button type="submit" name="submit" class="save-btn">Save Attendance</button>

<a href="dashboard.php" class="back-btn">← Back</a>

</form>

</div>

</body>
</html>