<?php
include 'connect.php';

$id = $_POST['id'];
$status = $_POST['status'];

mysqli_query($con,"UPDATE order_master SET status='$status' WHERE order_id='$id'");

header("location:dashboard.php");
?>