    <?php 
include('hhh.php'); 
include("connect.php");

// Get brand ID from URL and validate it
if(!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Invalid brand ID!'); window.location='view_brand.php';</script>";
    exit;
}

$id = intval($_GET['id']); // Convert to integer for safety

// Get current brand data with error checking
$q = mysqli_query($con, "SELECT * FROM brand_master WHERE bid = $id");

// Check if query executed successfully
if($q === false) {
    echo "<script>alert('Database error: " . addslashes(mysqli_error($con)) . "'); window.location='view_brand.php';</script>";
    exit;
}

// Check if brand exists
if(mysqli_num_rows($q) == 0) {
    echo "<script>alert('Brand with ID $id not found in database!'); window.location='view_brand.php';</script>";
    exit;
}

$row = mysqli_fetch_array($q);

// When form is submitted
if(isset($_POST["btnup"])) 
{
    $name = mysqli_real_escape_string($con, $_POST["txtname"]); // Prevent SQL injection
    $status = isset($_POST["status"]) ? intval($_POST["status"]) : $row['status']; // Handle status
    
    // Check if new logo is uploaded
    if($_FILES["txtlogo"]["name"] != "") 
    {
        // New logo uploaded - create unique filename
        $original_name = $_FILES["txtlogo"]["name"];
        $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
        $unique_name = time() . '_' . uniqid() . '.' . $file_extension;
        $dst = './images/' . $unique_name;
        
        // Update with new logo
        $q = mysqli_query($con, "UPDATE brand_master SET bname='$name', logo='$unique_name', status='$status' WHERE bid=$id");
        
        if($q) 
        {
            move_uploaded_file($_FILES["txtlogo"]["tmp_name"], $dst);
            
            // Delete old logo if it exists and no other brand is using it
            if(!empty($row['logo'])) {
                $check_logo = mysqli_query($con, "SELECT COUNT(*) as count FROM brand_master WHERE logo = '" . $row['logo'] . "'");
                $check_result = mysqli_fetch_assoc($check_logo);
                
                if($check_result['count'] == 0 && file_exists("./images/" . $row['logo'])) {
                    unlink("./images/" . $row['logo']);
                }
            }
            
            echo "<script>alert('Brand updated successfully!'); window.location='view_brand.php';</script>";
        }
        else 
        {
            echo "<script>alert('Error updating brand: " . mysqli_error($con) . "');</script>";
        }
    }
    else 
    {
        // No new logo, keep old logo
        $q = mysqli_query($con, "UPDATE brand_master SET bname='$name', status='$status' WHERE bid=$id");
        
        if($q) 
        {
            echo "<script>alert('Brand updated successfully!'); window.location='view_brand.php';</script>";
        }
        else 
        {
            echo "<script>alert('Error updating brand: " . mysqli_error($con) . "');</script>";
        }
    }
}
?>

<!-- Start Content-->
<div class="container-fluid">
    <div class="content-page">
        <div class="content">
    
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Update Brand</h4>
            </div>
        </div>
    </div>     
    <!-- end page title -->

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="header-title">Edit Brand Details</h4>
                    <a href="view_brand.php" class="btn btn-sm btn-secondary">
                        <i class="ri-arrow-left-line me-1"></i>Back to List
                    </a>
                </div>
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="txtname" class="form-label">
                                <strong>Brand Name:</strong>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="txtname" 
                                   name="txtname" 
                                   value="<?php echo htmlspecialchars($row['bname']); ?>" 
                                   required/>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <strong>Current Logo:</strong>
                            </label>
                            <div>
                                <?php if($row['logo'] != ""): ?>
                                    <img src="./images/<?php echo htmlspecialchars($row['logo']); ?>" 
                                         alt="Current logo" 
                                         style="max-width: 150px; max-height: 150px; object-fit: cover;" 
                                         class="rounded border">
                                    <p class="text-muted mt-2 mb-0"><small>Current: <?php echo htmlspecialchars($row['logo']); ?></small></p>
                                <?php else: ?>
                                    <p class="text-muted"><em>No logo uploaded</em></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="txtlogo" class="form-label">
                                <strong>Upload New Logo:</strong>
                            </label>
                            <input type="file" 
                                   class="form-control" 
                                   id="txtlogo" 
                                   name="txtlogo" 
                                   accept="image/*"/>
                            <small class="form-text text-muted">Leave empty to keep current logo</small>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">
                                <strong>Status:</strong>
                            </label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="1" <?php echo ($row['status'] == 1) ? 'selected' : ''; ?>>Active</option>
                                <option value="0" <?php echo ($row['status'] == 0) ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="view_brand.php" class="btn btn-light" style="background-color: #fff; color: #000; border: 1px solid #000;">
                                <i class="ri-close-line me-1"></i>Cancel
                            </a>
                            <button type="submit" name="btnup" class="btn btn-dark" style="background-color: #000; color: #fff;">
                                <i class="ri-save-line me-1"></i>Update Brand
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    </div>
    </div>
</div>
<!-- container-fluid -->

<?php include('fff.php'); ?>

