<?php
session_start();

$total = $_POST['total'];

$_SESSION['total'] = $total;

echo "done";
?>