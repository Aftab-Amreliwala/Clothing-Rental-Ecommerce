<?php include('hhh.php'); ?>

<!-- Start Content-->
<div class="container-fluid">
    <div class="content-page">
        <div class="content">
    
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">View Products</h4>
            </div>
        </div>
    </div>     
    <!-- end page title -->

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="header-title">All Products</h4>
                    <a href="add_product.php">
                        <i class="ri-add-line me-1"></i>Add New
                    </a>
                </div>
                <div class="card-body">
                    <?php
                    // Database connection
                    include("connect.php");
                    
                    // Get all columns from product_master table
                    $columns_result = mysqli_query($con, "SHOW COLUMNS FROM product_master");
                    $col_names = array();
                    $has_cid = false;
                    $has_scid = false;
                    $has_bid = false;
                    
                    if($columns_result) {
                        while($col = mysqli_fetch_assoc($columns_result)) {
                            $col_names[] = $col['Field'];
                            if($col['Field'] == 'cid') $has_cid = true;
                            if($col['Field'] == 'scid') $has_scid = true;
                            if($col['Field'] == 'bid') $has_bid = true;
                        }
                    }
                    
                    // Build base query
                    $sql = "SELECT * FROM product_master ORDER BY pid DESC";
                    $result = mysqli_query($con, $sql);
                    
                    // Check if query executed successfully
                    if ($result === false) {
                        echo "<div class='alert alert-danger'>Error executing query: " . mysqli_error($con) . "</div>";
                        echo "<div class='alert alert-info'>Please run the setup file first: <a href='setup_database.php'>setup_database.php</a></div>";
                    } elseif (mysqli_num_rows($result) > 0) {
                    ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 5%;">ID</th>
                                        <th style="width: 12%;">Photo</th>
                                        <th style="width: 20%;">Product Name</th>
                                        <?php if($has_cid): ?><th style="width: 10%;">Category ID</th><?php endif; ?>
                                        <?php if($has_scid): ?><th style="width: 10%;">Sub Cat ID</th><?php endif; ?>
                                        <?php if($has_bid): ?><th style="width: 10%;">Brand ID</th><?php endif; ?>
                                        <th style="width: 8%;">Qty</th>
                                        <th style="width: 10%;">price</th>
                                        <th style="width: 5%;">Status</th>
                                        <th style="width: 15%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row["pid"]) . "</td>";
                                        echo "<td>";
                                        if (!empty($row["photo"])) {
                                            echo "<img src='images/" . htmlspecialchars($row["photo"]) . "' alt='Product Image' style='width: 50px; height: 50px; object-fit: cover;' class='rounded'>";
                                        } else {
                                            echo "<span class='text-muted'>No Image</span>";
                                        }
                                        echo "</td>";
                                        echo "<td>" . htmlspecialchars($row["pname"]) . "</td>";
                                        if($has_cid) echo "<td>" . htmlspecialchars($row["cid"]) . "</td>";
                                        if($has_scid) echo "<td>" . htmlspecialchars($row["scid"]) . "</td>";
                                        if($has_bid) echo "<td>" . htmlspecialchars($row["bid"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["qty"]) . "</td>";
                                        echo "<td>$" . number_format($row["price"], 2) . "</td>";
                                        
                                        // Status column
                                        echo "<td>";
                                        if(isset($row["status"]) && $row["status"] == 1) {
                                            echo "<span class='badge bg-success'>Active</span>";
                                        } else {
                                            echo "<span class='badge bg-danger'>Inactive</span>";
                                        }
                                        echo "</td>";
                                        
                                        echo "<td>";
                                        
                                        // Edit Button
                                        echo "<a href='update_product.php?id=" . intval($row["pid"]) . "' class='btn btn-sm btn-dark me-1' style='background-color: #000; color: #fff;'>";
                                        echo "<i class='ri-edit-line'></i> Edit";
                                        echo "</a>";
                                        
                                        // Delete Button
                                        echo "<a href='delete_product.php?id=" . intval($row["pid"]) . "' class='btn btn-sm btn-light' style='background-color: #fff; color: #000; border: 1px solid #000;' onclick=\"return confirm('Are you sure you want to delete this product?')\">";
                                        echo "<i class='ri-delete-bin-line'></i> Delete";
                                        echo "</a>";
                                        
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Summary Info -->
                        <div class="mt-3">
                            <small class="text-muted">Total products: <?php echo mysqli_num_rows($result); ?></small>
                        </div>
                        
                    <?php
                    } else {
                        echo "<div class='alert alert-info text-center'>";
                        echo "<h5>No Products Found</h5>";
                        echo "<p>You haven't added any products yet.</p>";
                        echo "<a href='add_product.php' class='btn btn-primary'><i class='ri-add-line me-1'></i> Add Your First Product</a>";
                        echo "</div>";
                    }
                    
                    mysqli_close($con);
                    ?>
                </div>
            </div>
        </div>
    </div>

    </div>
    </div>
</div>
<!-- container-fluid -->

<?php include('fff.php'); ?>