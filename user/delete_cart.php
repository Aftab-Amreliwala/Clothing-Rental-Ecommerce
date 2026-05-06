<?php
session_start();
include '../admin/connect.php';

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];

mysqli_query($con, "DELETE FROM cart WHERE user_id='$user_id' AND product_id='$product_id'");

echo "removed";
?>
