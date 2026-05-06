<?php include('hhh.php'); ?>

<!-- Start Content-->
<div class="container-fluid">
    <div class="content-page">
        <div class="content">
    
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">View Sub Categories</h4>
            </div>
        </div>
    </div>     
    <!-- end page title -->

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="header-title">All Sub Categories</h4>
                    <a href="add_subcategory.php">
                        <i class="ri-add-line me-1"></i>Add New
                    </a>
                </div>
                <div class="card-body">
                    <?php
                    // Database connection
                    include("connect.php");
                    
                    $sql = "SELECT s.*, c.cname as category_name FROM subcategory_master s 
                            LEFT JOIN category_master c ON s.cid = c.cid 
                            ORDER BY s.sid DESC";
                    $result = mysqli_query($con, $sql);
                    
                    // Check if query executed successfully
                    if ($result === false) {
                        echo "<div class='alert alert-danger'>Error executing query: " . mysqli_error($con) . "</div>";
                    } elseif (mysqli_num_rows($result) > 0) {
                    ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 8%;">ID</th>
                                        <th style="width: 18%;">Category</th>
                                        <th style="width: 18%;">Sub Category Name</th>
                                        <th style="width: 20%;">Description</th>
                                        <th style="width: 12%;">Photo</th>
                                        <th style="width: 10%;">Status</th>
                                        <th style="width: 14%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row["sid"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["category_name"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["sname"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["description"]) . "</td>";
                                        echo "<td>";
                                        if (!empty($row["photo"])) {
                                            echo "<img src='images/" . htmlspecialchars($row["photo"]) . "' alt='Sub Category Image' style='width: 50px; height: 50px; object-fit: cover;' class='rounded'>";
                                        } else {
                                            echo "<span class='text-muted'>No Image</span>";
                                        }
                                        echo "</td>";
                                        
                                        // Status column
                                        echo "<td>";
                                        if($row["status"] == 1) {
                                            echo "<span class='badge bg-success'>Active</span>";
                                        } else {
                                            echo "<span class='badge bg-danger'>Inactive</span>";
                                        }
                                        echo "</td>";
                                        
                                        echo "<td>";
                                        
                                        // Edit Button - Black background with white text
                                        echo "<a href='update_subcategory.php?id=" . intval($row["sid"]) . "' class='btn btn-sm btn-dark me-1' style='background-color: #000; color: #fff;'>";
                                        echo "<i class='ri-edit-line'></i> Edit";
                                        echo "</a>";
                                        
                                        // Delete Button - White background with black text
                                        echo "<a href='delete_subcategory.php?id=" . intval($row["sid"]) . "' class='btn btn-sm btn-light' style='background-color: #fff; color: #000; border: 1px solid #000;' onclick=\"return confirm('Are you sure you want to delete this sub category?')\">";
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
                            <small class="text-muted">Total sub categories: <?php echo mysqli_num_rows($result); ?></small>
                        </div>
                        
                    <?php
                    } else {
                        echo "<div class='alert alert-info text-center'>";
                        echo "<h5>No Sub Categories Found</h5>";
                        echo "<p>You haven't added any sub categories yet.</p>";
                        echo "<a href='add_subcategory.php' class='btn btn-primary'><i class='ri-add-line me-1'></i> Add Your First Sub Category</a>";
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

