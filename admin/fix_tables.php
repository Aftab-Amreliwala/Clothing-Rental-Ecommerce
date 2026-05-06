<?php
include('hhh.php');
include('connect.php');

echo "<div class='container mt-4'>";
echo "<h2>Database Setup & Fix</h2>";

$errors = array();
$success = array();

// 1. Check and create category_master table
$check_cat = mysqli_query($con, "SHOW TABLES LIKE 'category_master'");
if(mysqli_num_rows($check_cat) == 0) {
    $sql = "CREATE TABLE category_master (
        cid INT AUTO_INCREMENT PRIMARY KEY,
        cname VARCHAR(255) NOT NULL,
        photo VARCHAR(255),
        status INT DEFAULT 1
    )";
    if(mysqli_query($con, $sql)) {
        $success[] = "Created category_master table";
    } else {
        $errors[] = "Error creating category_master: " . mysqli_error($con);
    }
} else {
    $success[] = "category_master table already exists";
}

// 2. Check and create brand_master table
$check_brand = mysqli_query($con, "SHOW TABLES LIKE 'brand_master'");
if(mysqli_num_rows($check_brand) == 0) {
    $sql = "CREATE TABLE brand_master (
        bid INT AUTO_INCREMENT PRIMARY KEY,
        bname VARCHAR(255) NOT NULL,
        logo VARCHAR(255),
        status INT DEFAULT 1
    )";
    if(mysqli_query($con, $sql)) {
        $success[] = "Created brand_master table";
    } else {
        $errors[] = "Error creating brand_master: " . mysqli_error($con);
    }
} else {
    $success[] = "brand_master table already exists";
}

// 3. Check and create subcategory_master table
$check_subcat = mysqli_query($con, "SHOW TABLES LIKE 'subcategory_master'");
if(mysqli_num_rows($check_subcat) == 0) {
    $sql = "CREATE TABLE subcategory_master (
        sid INT AUTO_INCREMENT PRIMARY KEY,
        cid INT NOT NULL,
        sname VARCHAR(255) NOT NULL,
        description TEXT,
        photo VARCHAR(255),
        status INT DEFAULT 1
    )";
    if(mysqli_query($con, $sql)) {
        $success[] = "Created subcategory_master table";
    } else {
        $errors[] = "Error creating subcategory_master: " . mysqli_error($con);
    }
} else {
    $success[] = "subcategory_master table already exists";
}

// 4. Check and create product_master table (drop and recreate)
$check_prod = mysqli_query($con, "SHOW TABLES LIKE 'product_master'");
if(mysqli_num_rows($check_prod) == 0) {
    $sql = "CREATE TABLE product_master (
        pid INT AUTO_INCREMENT PRIMARY KEY,
        cid INT NOT NULL,
        scid INT,
        bid INT,
        pname VARCHAR(255) NOT NULL,
        pdesc TEXT,
        qty INT DEFAULT 0,
        rate DECIMAL(10,2) DEFAULT 0,
        pdate DATE,
        photo VARCHAR(255),
        status INT DEFAULT 1
    )";
    if(mysqli_query($con, $sql)) {
        $success[] = "Created product_master table";
    } else {
        $errors[] = "Error creating product_master: " . mysqli_error($con);
    }
} else {
    // Table exists, let's check columns
    $cols = mysqli_query($con, "SHOW COLUMNS FROM product_master");
    $col_list = array();
    while($c = mysqli_fetch_assoc($cols)) {
        $col_list[] = $c['Field'];
    }
    
    // Add missing columns
    if(!in_array('cid', $col_list)) {
        mysqli_query($con, "ALTER TABLE product_master ADD cid INT AFTER pid");
        $success[] = "Added cid column to product_master";
    }
    if(!in_array('scid', $col_list)) {
        mysqli_query($con, "ALTER TABLE product_master ADD scid INT AFTER cid");
        $success[] = "Added scid column to product_master";
    }
    if(!in_array('bid', $col_list)) {
        mysqli_query($con, "ALTER TABLE product_master ADD bid INT AFTER scid");
        $success[] = "Added bid column to product_master";
    }
    if(!in_array('pname', $col_list)) {
        mysqli_query($con, "ALTER TABLE product_master ADD pname VARCHAR(255) AFTER bid");
        $success[] = "Added pname column to product_master";
    }
    if(!in_array('pdesc', $col_list)) {
        mysqli_query($con, "ALTER TABLE product_master ADD pdesc TEXT AFTER pname");
        $success[] = "Added pdesc column to product_master";
    }
    if(!in_array('qty', $col_list)) {
        mysqli_query($con, "ALTER TABLE product_master ADD qty INT DEFAULT 0 AFTER pdesc");
        $success[] = "Added qty column to product_master";
    }
    if(!in_array('rate', $col_list)) {
        mysqli_query($con, "ALTER TABLE product_master ADD rate DECIMAL(10,2) DEFAULT 0 AFTER qty");
        $success[] = "Added rate column to product_master";
    }
    if(!in_array('pdate', $col_list)) {
        mysqli_query($con, "ALTER TABLE product_master ADD pdate DATE AFTER rate");
        $success[] = "Added pdate column to product_master";
    }
    if(!in_array('photo', $col_list)) {
        mysqli_query($con, "ALTER TABLE product_master ADD photo VARCHAR(255) AFTER pdate");
        $success[] = "Added photo column to product_master";
    }
    if(!in_array('status', $col_list)) {
        mysqli_query($con, "ALTER TABLE product_master ADD status INT DEFAULT 1 AFTER photo");
        $success[] = "Added status column to product_master";
    }
    
    $success[] = "product_master table already exists - added missing columns";
}

// Show results
echo "<div class='alert alert-success'>";
echo "<h4>Success:</h4>";
foreach($success as $s) {
    echo "<div>✓ $s</div>";
}
echo "</div>";

if(!empty($errors)) {
    echo "<div class='alert alert-danger'>";
    echo "<h4>Errors:</h4>";
    foreach($errors as $e) {
        echo "<div>✗ $e</div>";
    }
    echo "</div>";
}

// Show final table structure
echo "<h3>Final Table Structures:</h3>";

$tables = array('category_master', 'brand_master', 'subcategory_master', 'product_master');

foreach($tables as $table) {
    echo "<h4>$table</h4>";
    $cols = mysqli_query($con, "SHOW COLUMNS FROM $table");
    echo "<table class='table table-bordered' style='width:50%'>";
    echo "<tr><th>Field</th><th>Type</th></tr>";
    while($c = mysqli_fetch_assoc($cols)) {
        echo "<tr><td>" . $c['Field'] . "</td><td>" . $c['Type'] . "</td></tr>";
    }
    echo "</table>";
}

echo "<hr>";
echo "<a href='dashboard.php' class='btn btn-primary'>Go to Dashboard</a>";
echo " <a href='add_product.php' class='btn btn-success'>Go to Add Product</a>";
echo " <a href='view_product.php' class='btn btn-info'>Go to View Product</a>";

echo "</div>";

include('fff.php');
?>

