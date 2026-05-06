<?php
session_start();
include('../admin/connect.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$user_query = "SELECT fname, lname, email, mobno, gender FROM user_master WHERE uid = ?";
$stmt = mysqli_prepare($con, $user_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user_result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($user_result);
mysqli_stmt_close($stmt);

// Wishlist count (error-safe)
$wishlist_count = 0;
$table_check = mysqli_query($con, "SHOW TABLES LIKE 'wishlist'");
if (mysqli_num_rows($table_check) > 0) {
    $wishlist_count_query = "SELECT COUNT(*) as count FROM wishlist WHERE uid = ?";
    $stmt = mysqli_prepare($con, $wishlist_count_query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $wishlist_result = mysqli_stmt_get_result($stmt);
        $wishlist_data = mysqli_fetch_assoc($wishlist_result);
        $wishlist_count = $wishlist_data ? (int)$wishlist_data['count'] : 0;
        mysqli_stmt_close($stmt);
    }
}

// Orders count (error-safe)
$orders_count = 0;
$table_check = mysqli_query($con, "SHOW TABLES LIKE 'orders'");
if (mysqli_num_rows($table_check) > 0) {
    $orders_count_query = "SELECT COUNT(*) as count FROM orders WHERE uid = ? AND status != 'cancelled'";
    $stmt = mysqli_prepare($con, $orders_count_query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $orders_result = mysqli_stmt_get_result($stmt);
        $orders_data = mysqli_fetch_assoc($orders_result);
        $orders_count = $orders_data ? (int)$orders_data['count'] : 0;
        mysqli_stmt_close($stmt);
    }
}

// Recent orders (last 5) - error-safe
$recent_orders = [];
$table_check = mysqli_query($con, "SHOW TABLES LIKE 'orders'");
if (mysqli_num_rows($table_check) > 0) {
    $recent_orders_query = "SELECT oid, order_date, total, status FROM orders WHERE uid = ? ORDER BY order_date DESC LIMIT 5";
    $stmt = mysqli_prepare($con, $recent_orders_query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $recent_orders_result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($recent_orders_result)) {
            $recent_orders[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
}

// Wishlist products - error-safe
$wishlist_products = [];
$table_check = mysqli_query($con, "SHOW TABLES LIKE 'wishlist'");
if (mysqli_num_rows($table_check) > 0) {
    $wishlist_products_query = "SELECT p.pid, p.pname, p.price, p.photo, w.added_date 
                               FROM wishlist w 
                               JOIN product_master p ON w.pid = p.pid 
                               WHERE w.uid = ? ORDER BY w.added_date DESC LIMIT 6";
    $stmt = mysqli_prepare($con, $wishlist_products_query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $wishlist_products_result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($wishlist_products_result)) {
            $wishlist_products[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Elite Shoppy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --teal: #00c4b4; --black: #111; --white: #fff; --light-gray: #f8f9fa;
            --font-display: 'Playfair Display', serif; --font-body: 'DM Sans', sans-serif;
        }
        body { font-family: var(--font-body); background: var(--light-gray); }
        .profile-header { background: linear-gradient(135deg, var(--black) 0%, #2d2d2d 100%); color: white; padding: 60px 0; }
        .profile-avatar { width: 120px; height: 120px; border-radius: 50%; background: var(--teal); margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; font-size: 48px; }
        .profile-name { font-family: var(--font-display); font-size: 36px; font-weight: 700; margin-bottom: 8px; }
        .profile-stats { display: flex; justify-content: center; gap: 40px; margin-top: 30px; }
        .stat { text-align: center; }
        .stat-number { font-size: 28px; font-weight: 700; color: var(--teal); }
        .stat-label { font-size: 14px; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px; }
        .section-card { background: white; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); padding: 30px; margin-bottom: 30px; }
        .section-title { font-family: var(--font-display); font-size: 24px; font-weight: 700; color: var(--black); margin-bottom: 25px; display: flex; align-items: center; gap: 12px; }
        .user-detail-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eee; }
        .user-detail-row:last-child { border-bottom: none; }
        .detail-label { font-weight: 600; color: #666; }
        .detail-value { font-weight: 500; color: var(--black); }
        .wishlist-grid, .orders-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .wish-item, .order-item { border: 1px solid #eee; border-radius: 12px; padding: 20px; text-align: center; transition: box-shadow 0.3s; }
        .wish-item:hover, .order-item:hover { box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        .wish-img { width: 100%; height: 160px; background: #f5f5f5; border-radius: 8px; margin-bottom: 12px; display: flex; align-items: center; justify-content: center; }
        .wish-name { font-weight: 600; margin-bottom: 8px; }
        .wish-price { font-size: 18px; font-weight: 700; color: var(--teal); }
        .order-status { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-confirmed { background: #d1ecf1; color: #0c5460; }
        .status-shipped { background: #d4edda; color: #155724; }
        .status-delivered { background: #cce5ff; color: #004085; }
        @media (max-width: 768px) { .profile-stats { flex-direction: column; gap: 20px; } }
    </style>
</head>
<body>
    <!-- Profile Header -->
    <section class="profile-header">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 text-center">
                    <div class="profile-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="profile-name"><?php echo htmlspecialchars($user['fname'] . ' ' . $user['lname']); ?></div>
                    <p class="lead mb-0"><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
            </div>
            <div class="profile-stats">
                <div class="stat">
                    <div class="stat-number"><?php echo $wishlist_count; ?></div>
                    <div class="stat-label">Wishlist</div>
                </div>
                <div class="stat">
                    <div class="stat-number"><?php echo $orders_count; ?></div>
                    <div class="stat-label">Orders</div>
                </div>
            </div>
        </div>
    </section>

    <div class="container my-5">
        <!-- User Details -->
        <div class="row">
            <div class="col-lg-8">
                <div class="section-card">
                    <h3 class="section-title"><i class="fas fa-user-circle"></i> Account Details</h3>
                    <div class="user-detail-row">
                        <span class="detail-label">Full Name:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($user['fname'] . ' ' . $user['lname']); ?></span>
                    </div>
                    <div class="user-detail-row">
                        <span class="detail-label">Email:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($user['email']); ?></span>
                    </div>
                    <div class="user-detail-row">
                        <span class="detail-label">Phone:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($user['mobno'] ?: 'Not provided'); ?></span>
                    </div>
                    <div class="user-detail-row">
                        <span class="detail-label">Gender:</span>
                        <span class="detail-value"><?php echo ucfirst($user['gender'] ?: 'Not specified'); ?></span>
                    </div>
                    <a href="registration.php" class="btn btn-outline-primary mt-3"><i class="fas fa-edit"></i> Edit Profile</a>
                </div>

                <!-- Recent Orders -->
                <div class="section-card">
                    <h3 class="section-title"><i class="fas fa-shopping-bag"></i> Recent Orders (<?php echo $orders_count; ?>)</h3>
                    <?php if (empty($recent_orders)): ?>
                        <p class="text-muted text-center py-4">No orders yet. <a href="Home.php">Start shopping!</a></p>
                    <?php else: ?>
                        <div class="orders-grid">
                            <?php foreach ($recent_orders as $order): ?>
                                <div class="order-item">
                                    <div class="mb-2">Order #<?php echo $order['oid']; ?></div>
                                    <div class="mb-3"><strong>₹<?php echo number_format($order['total'], 2); ?></strong></div>
                                    <div class="mb-2"><?php echo date('M j, Y', strtotime($order['order_date'])); ?></div>
                                    <span class="order-status status-<?php echo strtolower($order['status']); ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Wishlist Sidebar -->
            <div class="col-lg-4">
                <div class="section-card">
                    <h3 class="section-title"><i class="fas fa-heart"></i> Wishlist (<?php echo $wishlist_count; ?>)</h3>
                    <?php if (empty($wishlist_products)): ?>
                        <p class="text-muted text-center py-4">Your wishlist is empty. <a href="Home.php">Browse products!</a></p>
                    <?php else: ?>
                        <div class="wishlist-grid">
                            <?php foreach ($wishlist_products as $product): ?>
                                <div class="wish-item">
                                    <div class="wish-img">
                                        <?php if (!empty($product['photo'])): ?>
                                            <img src="../admin/images/<?php echo htmlspecialchars($product['photo']); ?>" alt="">
                                        <?php else: ?>
                                            <i class="fas fa-tshirt" style="font-size: 32px; color: #ccc;"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="wish-name"><?php echo htmlspecialchars($product['pname']); ?></div>
                                    <div class="wish-price">₹<?php echo number_format($product['price'], 0); ?></div>
                                    <small class="text-muted"><?php echo date('M j', strtotime($product['added_date'])); ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <a href="#" class="btn btn-outline-teal w-100 mt-3">View All Wishlist</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
