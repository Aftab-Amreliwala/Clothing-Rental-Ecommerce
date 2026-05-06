<?php
session_start();
include '../admin/connect.php';

if (!isset($_SESSION['user_id'])) {
    echo "login_required";
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];

// ✅ Correct table name
$product_query = mysqli_query($con, "SELECT pname, price FROM product_master WHERE pid='$product_id'");

if (!$product_query) {
    die("Query Error: " . mysqli_error($con));
}

$product = mysqli_fetch_assoc($product_query);

if (!$product) {
    die("Product not found");
}

$product_name = $product['pname'];
$price = $product['price'];

// Check if already exists
$check = mysqli_query($con, "SELECT * FROM cart WHERE user_id='$user_id' AND product_id='$product_id'");

if (mysqli_num_rows($check) > 0) {
    mysqli_query($con, "UPDATE cart SET quantity = quantity + 1 WHERE user_id='$user_id' AND product_id='$product_id'");
} else {
    mysqli_query($con, "INSERT INTO cart (user_id, product_id, product_name, price, quantity)
    VALUES ('$user_id', '$product_id', '$product_name', '$price', 1)");
}

echo "added";
?>