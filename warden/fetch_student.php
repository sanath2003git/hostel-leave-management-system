<?php
include("../config/db.php");

if(isset($_GET['reg_no'])){
    $reg_no = $_GET['reg_no'];

   $stmt = $conn->prepare("
    SELECT name, email, parent_email, teacher_email,
           department, year, phone
    FROM student_list__mca_2k25
    WHERE register_number = ?
");

    $stmt->bind_param("s", $reg_no);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode([]);
    }
}
?>