<?php
include('hhh.php');
include('connect.php');

// Get categories for dropdown
$categories = [];
$cat_query = "SELECT * FROM category_master WHERE status = 1 ORDER BY cname ASC";
$cat_result = mysqli_query($con, $cat_query);
if($cat_result){
    while($row = mysqli_fetch_assoc($cat_result)){
        $categories[] = $row;
    }
}

if(isset($_POST["submit"])){
    
    // Get data from form
    $cid = $_POST["cid"];
    $sname = $_POST["sname"];
    $desc = $_POST["description"];
    $photo_name = $_FILES["photo"]['name'];
    $status = $_POST["status"];
    
    // Convert status to number (1 = Active, 0 = Inactive)
    if($status == 'Active') {
        $status_value = 1;
    } else {
        $status_value = 0;
    }
    
    // Check if photo is uploaded
    if(!empty($photo_name)){
        
        // Create unique name for photo
        $unique_photo_name = time() . '_' . $photo_name;
        
        // Where to save the photo
        $save_path = './images/' . $unique_photo_name;
        
        // Move the uploaded file first
        if(move_uploaded_file($_FILES["photo"]['tmp_name'], $save_path)) {
            // Then save to database (escape special characters)
            $sname_escaped = mysqli_real_escape_string($con, $sname);
            $desc_escaped = mysqli_real_escape_string($con, $desc);
            $query = "INSERT INTO subcategory_master (cid, sname, description, photo, status) VALUES ('$cid', '$sname_escaped', '$desc_escaped', '$unique_photo_name', '$status_value')";
            $result = mysqli_query($con, $query);
            
            if($result) {
                echo "<div class='alert alert-success'>✓ Subcategory added successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>✗ Error: " . mysqli_error($con) . "</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>✗ Error uploading file!</div>";
        }
    
    } else {
        echo "<div class='alert alert-warning'>⚠ Please select a photo!</div>";
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
                <h4 class="page-title">Add Subcategory</h4>
            </div>
    </div>     
    <!-- end page title -->

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Subcategory Information</h4>
                </div>
                <div class="card-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        
                        <div class="mb-3">
                            <label for="cid" class="form-label">Category</label>
                            <select name="cid" class="form-control" required>
                                <option value="">Select Category</option>
                                <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat['cid']; ?>"><?php echo htmlspecialchars($cat['cname']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="sname" class="form-label">Subcategory Name</label>
                            <input type="text" class="form-control" id="sname" name="sname" placeholder="Enter subcategory name" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter description"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="photo" class="form-label">Subcategory Photo</label>
                            <input type="file" class="form-control" id="photo" name="photo" accept="image/*" required>
                            <small class="form-text text-muted">Supported formats: JPG, PNG, GIF. Max size: 2MB</small>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" class="form-control" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="text-center">
                            <button type="submit" name="submit" class="btn btn-primary waves-effect waves-light">
                                <i class="ri-save-line me-1"></i> Submit
                            </button>
                            <button type="reset" class="btn btn-secondary waves-effect waves-light">
                                <i class="ri-refresh-line me-1"></i> Reset
                            </button>
                        </div>

                    </form>
                </div>
        </div>

    </div>
</div>
<!-- container-fluid -->

<?php include('fff.php'); ?>
