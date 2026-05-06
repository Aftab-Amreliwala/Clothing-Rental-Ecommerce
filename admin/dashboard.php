<?php
session_start();
include 'connect.php';

if(!isset($_SESSION['admin_id'])){
    header("location:admin_login.php");
}

// ===== COUNTS =====
$category = mysqli_num_rows(mysqli_query($con,"SELECT * FROM category_master"));
$product = mysqli_num_rows(mysqli_query($con,"SELECT * FROM product_master"));
$order = mysqli_num_rows(mysqli_query($con,"SELECT * FROM order_master"));
$user = mysqli_num_rows(mysqli_query($con,"SELECT * FROM user_master"));

// ===== TODAY ORDERS =====
$today = date('Y-m-d');
$today_orders = mysqli_num_rows(mysqli_query($con,
"SELECT * FROM order_master WHERE DATE(order_date)='$today'"));

// ===== TOTAL REVENUE =====
$rev = mysqli_fetch_assoc(mysqli_query($con,
"SELECT SUM(total) as total FROM order_master"));

$revenue = $rev['total'] ?? 0;

// ===== RECENT ORDERS =====
$recent = mysqli_query($con,"SELECT * FROM order_master ORDER BY order_id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body{background:#f5f6fa;}

.card-box{
    padding:20px;
    border-radius:10px;
    color:#fff;
    margin-bottom:20px;
}

.bg1{background:#6c5ce7;}
.bg2{background:#00b894;}
.bg3{background:#0984e3;}
.bg4{background:#d63031;}
.bg5{background:#fdcb6e; color:#000;}

.table{
    background:#fff;
}
</style>
</head>

<body>

<?php include 'hhh.php'; ?>

<div class="container mt-4">

<h3 class="mb-4">Dashboard</h3>

<div class="row">

<div class="col-md-3">
<div class="card-box bg1">
<h4><?php echo $category; ?></h4>
<p>Categories</p>
</div>
</div>

<div class="col-md-3">
<div class="card-box bg2">
<h4><?php echo $product; ?></h4>
<p>Products</p>
</div>
</div>

<div class="col-md-3">
<div class="card-box bg3">
<h4><?php echo $order; ?></h4>
<p>Total Orders</p>
</div>
</div>

<div class="col-md-3">
<div class="card-box bg4">
<h4><?php echo $user; ?></h4>
<p>Users</p>
</div>
</div>

<div class="col-md-3">
<div class="card-box bg5">
<h4><?php echo $today_orders; ?></h4>
<p>Today Orders</p>
</div>
</div>

<div class="col-md-3">
<div class="card-box bg1">
<h4>₹<?php echo $revenue; ?></h4>
<p>Total Revenue</p>
</div>
</div>

</div>

<!-- ===== CHART ===== -->
<div class="card p-3 mb-4">
<h5>Sales Overview</h5>
<canvas id="salesChart"></canvas>
</div>

<!-- ===== RECENT ORDERS ===== -->
<div class="card p-3">
<h5>Recent Orders</h5>

<table class="table table-bordered">
<tr>
<th>ID</th>
<th>Name</th>
<th>Total</th>
<th>Date</th>
<th>Status</th>
</tr>

<?php while($row=mysqli_fetch_assoc($recent)){ ?>
<tr>
<td><?php echo $row['order_id']; ?></td>
<td><?php echo $row['user_name']; ?></td>
<td>₹<?php echo $row['total']; ?></td>
<td><?php echo $row['order_date']; ?></td>
<td>


<!-- DROPDOWN -->
<form method="post" action="change_status.php" class="d-flex gap-1">
    
    <select name="status" class="form-select form-select-sm">
        <option value="0" <?php if($row['status']==0) echo "selected"; ?>>Pending</option>
        <option value="1" <?php if($row['status']==1) echo "selected"; ?>>Confirmed</option>
        <option value="2" <?php if($row['status']==2) echo "selected"; ?>>Delivered</option>
    </select>

    <input type="hidden" name="id" value="<?php echo $row['order_id']; ?>">

    <button class="btn btn-sm btn-primary">
        ✔
    </button>

</form>

</td>
</tr>
<?php } ?>

</table>
</div>

</div>

<!-- ===== CHART SCRIPT ===== -->
<script>
const ctx = document.getElementById('salesChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Orders', 'Users', 'Products'],
        datasets: [{
            label: 'Dashboard Data',
            data: [
                <?php echo $order; ?>,
                <?php echo $user; ?>,
                <?php echo $product; ?>
            ]
        }]
    }
});
</script>

<?php include 'fff.php'; ?>

</body>
</html>