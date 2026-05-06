<?php
session_start();
include '../admin/connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];

// 🔥 JOIN WITH product_master
$query = "
SELECT 
    c.product_id,
    c.product_name,
    c.price,
    c.quantity,
    p.photo
FROM cart c
JOIN product_master p ON c.product_id = p.pid
WHERE c.user_id = '$user_id'
";

$result = mysqli_query($con, $query);

$cart = [];

while ($row = mysqli_fetch_assoc($result)) {
    $cart[] = [
        "product_id" => $row['product_id'],
        "product_name" => $row['product_name'],
        "price" => $row['price'],
        "quantity" => $row['quantity'],
        "photo" => $row['photo']
    ];
}

echo json_encode($cart);
?>