<?php
include("../includes/auth_check.php");
include("../config/db.php");

// Fetch students
$query = "SELECT * FROM users WHERE role_id = 1";
$result = mysqli_query($conn, $query);

// Handle form submit
if (isset($_POST['submit'])) {

    $date = date("Y-m-d");
    $warden_id = $_SESSION['user_id'];

    foreach ($_POST['attendance'] as $user_id => $status) {

        $leaveQuery = "
        SELECT * FROM hostel_leaves 
        WHERE student_id = ? 
        AND status='Approved'
        AND DATE(from_datetime) <= ?
        AND DATE(to_datetime) >= ?
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
            INSERT INTO attendance (user_id,date,status,remark,marked_by)
            VALUES (?,?,?,?,?)
            ON DUPLICATE KEY UPDATE
            status=VALUES(status),
            remark=VALUES(remark)
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

.logo{
font-size:18px;
font-weight:600;
}

.logout-btn{
padding:8px 16px;
border-radius:8px;
background:#111;
color:#fff;
text-decoration:none;
font-size:14px;
margin-left:12px;
transition:0.2s;
}

.logout-btn:hover{
background:rgba(255,255,255,0.15);
}

/* MAIN BOX */

.dashboard{
max-width:1000px;
margin:auto;
padding:50px;
background:#fff;
border-radius:22px;
box-shadow:
0 40px 90px rgba(0,0,0,0.12),
0 15px 35px rgba(0,0,0,0.08);
}

h1{
font-size:30px;
margin-bottom:10px;
}

.subtitle{
color:#777;
font-size:14px;
margin-bottom:30px;
}

/* SUCCESS */

.success{
padding:14px;
background:#eafaf1;
color:#27ae60;
border-radius:10px;
margin-bottom:25px;
font-size:14px;
}

/* TABLE */

table{
width:100%;
border-collapse:collapse;
margin-top:15px;
}

th{
background:#111;
color:#fff;
padding:14px;
text-align:left;
font-size:14px;
}

td{
padding:14px;
border-bottom:1px solid #eee;
font-size:14px;
}

tr:hover{
background:#f8f9fb;
}

/* SELECT */

select{
padding:10px 14px;
border:1px solid #ddd;
border-radius:8px;
font-size:14px;
outline:none;
background:#fff;
}

/* BUTTON */

.btn{
margin-top:25px;
padding:12px 22px;
background:#111;
color:#fff;
border:none;
border-radius:10px;
font-size:14px;
font-weight:500;
cursor:pointer;
transition:0.2s;
}

.btn:hover{
background:#444;
}

/* BACK */

.back-btn{
display:inline-block;
margin-top:25px;
margin-left:12px;
padding:12px 22px;
background:#111;
color:#fff;
text-decoration:none;
border-radius:10px;
font-size:14px;
transition:0.2s;
}

.back-btn:hover{
background:#444;
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

<div class="dashboard">

<h1>Attendance</h1>

<div class="subtitle">
Mark student daily attendance records.
</div>

<?php if(isset($success)) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>

<form method="POST">

<table>

<tr>
<th>Student Name</th>
<th>Status</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>

<tr>
<td><?php echo $row['name']; ?></td>

<td>
<select name="attendance[<?php echo $row['id']; ?>]">
<option value="Present">Present</option>
<option value="Absent">Absent</option>
</select>
</td>
</tr>

<?php } ?>

</table>

<button type="submit" name="submit" class="btn">Save Attendance</button>

<a href="dashboard.php" class="back-btn">Back</a>

</form>

</div>

</body>
</html>