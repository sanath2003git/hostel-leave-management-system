<?php
include("../includes/auth_check.php");
include("../config/db.php");
include("../config/mail_config.php");

date_default_timezone_set("Asia/Kolkata");

if ($_SESSION["role"] != "warden") {
    echo "Access Denied";
    exit();
}

$success = "";

/* SAVE ATTENDANCE */
if(isset($_POST["save_attendance"])){

    $date = date("Y-m-d");

    foreach($_POST["status"] as $student_id => $status){

        $remark = "Normal";
        $sendUnauthorizedMail = false;

        /* If marked Absent, check approved leave */
        if($status == "Absent"){

            $checkLeave = $conn->prepare("
            SELECT id
            FROM hostel_leaves
            WHERE student_id = ?
            AND status = 'Approved'
            AND ? BETWEEN DATE(from_datetime) AND DATE(to_datetime)
            AND returned_at IS NULL
            LIMIT 1
            ");

            $checkLeave->bind_param("is", $student_id, $date);
            $checkLeave->execute();
            $leaveResult = $checkLeave->get_result();

            if($leaveResult->num_rows > 0){

                $status = "Leave";
                $remark = "Approved Leave";

            }else{

                $remark = "Unauthorized";
                $sendUnauthorizedMail = true;
            }
        }

        /* Check if attendance already exists today */
        $check = $conn->prepare("
        SELECT id
        FROM attendance
        WHERE user_id = ?
        AND date = ?
        ");

        $check->bind_param("is", $student_id, $date);
        $check->execute();
        $already = $check->get_result();

        if($already->num_rows > 0){

            $update = $conn->prepare("
            UPDATE attendance
            SET status = ?, remark = ?
            WHERE user_id = ?
            AND date = ?
            ");

            $update->bind_param("ssis", $status, $remark, $student_id, $date);
            $update->execute();

        }else{

            $insert = $conn->prepare("
            INSERT INTO attendance(user_id,date,status,remark)
            VALUES(?,?,?,?)
            ");

            $insert->bind_param("isss", $student_id, $date, $status, $remark);
            $insert->execute();
        }

        /* SEND EMAIL TO PARENT + TEACHER */
        if($sendUnauthorizedMail){

            $getUser = $conn->prepare("
            SELECT name, parent_email, teacher_email
            FROM users
            WHERE id = ?
            ");

            $getUser->bind_param("i", $student_id);
            $getUser->execute();

            $res = $getUser->get_result();
            $user = $res->fetch_assoc();

            if($user){

                $subject = "Unauthorized Absence Alert";

                $body = "
                <h3>Attendance Alert</h3>
                <p><strong>Student:</strong> {$user['name']}</p>
                <p><strong>Date:</strong> {$date}</p>
                <p><strong>Status:</strong> Absent without approved leave</p>
                <p>Please take necessary action.</p>
                ";

                if(!empty($user["parent_email"])){
                    sendMail($user["parent_email"], $subject, $body);
                }

                if(!empty($user["teacher_email"])){
                    sendMail($user["teacher_email"], $subject, $body);
                }
            }
        }
    }

    $success = "Attendance saved successfully.";
}

/* FETCH STUDENTS */
$students = $conn->query("
SELECT users.id,
       users.name,
       student_profiles.department,
       student_profiles.room_number
FROM users
JOIN roles ON users.role_id = roles.id
LEFT JOIN student_profiles ON users.id = student_profiles.user_id
WHERE roles.role_name = 'student'
ORDER BY users.name ASC
");
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
background:linear-gradient(135deg,#dcdde1,#eceef2,#d6d8de);
padding:120px 50px 50px;
}

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
padding:0 50px;
z-index:1000;
}

.topbar a{
color:#fff;
text-decoration:none;
margin-left:20px;
}

.container{
max-width:1150px;
margin:auto;
background:#fff;
padding:50px;
border-radius:22px;
box-shadow:
0 30px 80px rgba(0,0,0,0.12),
0 10px 25px rgba(0,0,0,0.08);
}

h1{
font-size:34px;
margin-bottom:8px;
}

.sub{
color:#666;
margin-bottom:25px;
font-size:14px;
}

.success{
background:#eafaf1;
color:#27ae60;
padding:12px;
border-radius:8px;
margin-bottom:20px;
}

table{
width:100%;
border-collapse:collapse;
margin-top:20px;
}

th{
background:#111;
color:#fff;
padding:14px;
text-align:left;
}

td{
padding:14px;
border-bottom:1px solid #eee;
}

select{
padding:8px 10px;
border-radius:8px;
border:1px solid #ccc;
outline:none;
}

button{
margin-top:25px;
padding:12px 20px;
border:none;
border-radius:8px;
background:#111;
color:#fff;
cursor:pointer;
}

button:hover{
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
</head>

<body>

<div class="topbar">
<div>Hostel Leave System</div>

<div>
<?php echo $_SESSION["username"]; ?> |
<a href="../auth/logout.php">Logout</a>
</div>
</div>

<div class="container">

<h1>Daily Attendance</h1>
<div class="sub">Absent students with approved leave will be marked as Leave automatically.</div>

<?php if($success != ""){ ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>

<form method="POST">

<table>

<tr>
<th>Name</th>
<th>Department</th>
<th>Room</th>
<th>Status</th>
</tr>

<?php while($row = $students->fetch_assoc()){ ?>

<tr>
<td><?php echo $row["name"]; ?></td>
<td><?php echo $row["department"]; ?></td>
<td><?php echo $row["room_number"]; ?></td>
<td>

<select name="status[<?php echo $row["id"]; ?>]">
<option value="Present">Present</option>
<option value="Absent">Absent</option>
</select>

</td>
</tr>

<?php } ?>

</table>

<button type="submit" name="save_attendance">Save Attendance</button>

<a href="dashboard.php" class="back-btn">← Back to Dashboard</a>

</form>

</div>

</body>
</html>