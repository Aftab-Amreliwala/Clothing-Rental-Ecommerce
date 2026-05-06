<?php
include('hhh.php');
include('connect.php');

$message = "";

// Create brand_master table
$sql1 = "CREATE TABLE IF NOT EXISTS brand_master (
    bid INT AUTO_INCREMENT PRIMARY KEY,
    bname VARCHAR(255) NOT NULL,
    logo VARCHAR(255) NOT NULL,
    status INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if(mysqli_query($con, $sql1)) {
    $message .= "<div class='alert alert-success'>✓ Brand table created successfully!</div>";
} else {
    $message .= "<div class='alert alert-danger'>✗ Error creating brand table: " . mysqli_error($con) . "</div>";
}

// Create subcategory_master table
$sql2 = "CREATE TABLE IF NOT EXISTS subcategory_master (
    sid INT AUTO_INCREMENT PRIMARY KEY,
    cid INT NOT NULL,
    sname VARCHAR(255) NOT NULL,
    description TEXT,
    photo VARCHAR(255),
    status INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if(mysqli_query($con, $sql2)) {
    $message .= "<div class='alert alert-success'>✓ Subcategory table created successfully!</div>";
} else {
    $message .= "<div class='alert alert-danger'>✗ Error creating subcategory table: " . mysqli_error($con) . "</div>";
}

// Create product_master table (drop if exists and recreate)
$sql3 = "DROP TABLE IF EXISTS product_master";

mysqli_query($con, $sql3);

$sql4 = "CREATE TABLE product_master (
    pid INT AUTO_INCREMENT PRIMARY KEY,
    cid INT NOT NULL,
    scid INT DEFAULT NULL,
    bid INT DEFAULT NULL,
    pname VARCHAR(255) NOT NULL,
    pdesc TEXT,
    qty INT DEFAULT 0,
    rate DECIMAL(10,2) DEFAULT 0.00,
    pdate DATE,
    photo VARCHAR(255),
    status INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if(mysqli_query($con, $sql4)) {
    $message .= "<div class='alert alert-success'>✓ Product table created successfully!</div>";
} else {
    $message .= "<div class='alert alert-danger'>✗ Error creating product table: " . mysqli_error($con) . "</div>";
}

// Show success message
echo $message;

// ── NEW TABLES FOR USER PROFILE ──

// Create wishlist table
$sql_wishlist = "CREATE TABLE IF NOT EXISTS wishlist (
    wid INT AUTO_INCREMENT PRIMARY KEY,
    uid INT NOT NULL,
    pid INT NOT NULL,
    added_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_product (uid, pid),
    FOREIGN KEY (uid) REFERENCES user_master(uid) ON DELETE CASCADE,
    FOREIGN KEY (pid) REFERENCES product_master(pid) ON DELETE CASCADE
)";

if(mysqli_query($con, $sql_wishlist)) {
    $message .= "<div class='alert alert-success'>✓ Wishlist table created successfully!</div>";
} else {
    $message .= "<div class='alert alert-danger'>✗ Error creating wishlist table: " . mysqli_error($con) . "</div>";
}

// Create orders table
$sql_orders = "CREATE TABLE IF NOT EXISTS orders (
    oid INT AUTO_INCREMENT PRIMARY KEY,
    uid INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    subtotal DECIMAL(10,2) DEFAULT 0,
    shipping DECIMAL(8,2) DEFAULT 0,
    tax DECIMAL(8,2) DEFAULT 0,
    discount DECIMAL(8,2) DEFAULT 0,
    total DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50),
    status ENUM('pending','confirmed','shipped','delivered','cancelled') DEFAULT 'pending',
    FOREIGN KEY (uid) REFERENCES user_master(uid) ON DELETE CASCADE
)";

if(mysqli_query($con, $sql_orders)) {
    $message .= "<div class='alert alert-success'>✓ Orders table created successfully!</div>";
} else {
    $message .= "<div class='alert alert-danger'>✗ Error creating orders table: " . mysqli_error($con) . "</div>";
}

// Create order_items table
$sql_order_items = "CREATE TABLE IF NOT EXISTS order_items (
    oiid INT AUTO_INCREMENT PRIMARY KEY,
    oid INT NOT NULL,
    pid INT NOT NULL,
    qty INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (oid) REFERENCES orders(oid) ON DELETE CASCADE,
    FOREIGN KEY (pid) REFERENCES product_master(pid)
)";

if(mysqli_query($con, $sql_order_items)) {
    $message .= "<div class='alert alert-success'>✓ Order Items table created successfully!</div>";
} else {
    $message .= "<div class='alert alert-danger'>✗ Error creating order_items table: " . mysqli_error($con) . "</div>";
}

echo $message;

// Show table structure
echo "<div class='container-fluid mt-4'>";
echo "<h4>Current Tables in Database:</h4>";
$tables = mysqli_query($con, "SHOW TABLES");
echo "<ul>";
while($row = mysqli_fetch_array($tables)) {
    echo "<li><strong>" . $row[0] . "</strong></li>";
}
echo "</ul>";

echo "<h4>Product Table Structure:</h4>";
$fields = mysqli_query($con, "DESCRIBE product_master");
echo "<table class='table table-bordered table-striped'>";
echo "<tr><th>Field</th><th>Type</th><th>Key</th></tr>";
while($row = mysqli_fetch_assoc($fields)) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Key'] . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<hr>";
echo "<a href='dashboard.php' class='btn btn-primary'>Go to Dashboard</a>";
echo "</div>";

include('fff.php');
?>

