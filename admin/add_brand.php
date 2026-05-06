<?php
include('hhh.php');
include('connect.php');

if(isset($_POST["submit"])){
    
    // Get data from form
    $brand_name = $_POST["bname"];
    $logo_name = $_FILES["logo"]['name'];
    $status = $_POST["status"];
    
    // Convert status to number (1 = Active, 0 = Inactive)
    if($status == 'Active') {
        $status_value = 1;
    } else {
        $status_value = 0;
    }
    
    // Check if logo is uploaded
    if(!empty($logo_name)){
        
        // Create unique name for logo
        $unique_logo_name = time() . '_' . $logo_name;
        
        // Where to save the logo
        $save_path = './images/' . $unique_logo_name;
        
        // First move the uploaded file
        if(move_uploaded_file($_FILES["logo"]['tmp_name'], $save_path)) {
            // Then save to database (escape special characters)
            $brand_name_escaped = mysqli_real_escape_string($con, $brand_name);
            $query = "INSERT INTO brand_master (bname, logo, status) VALUES ('$brand_name_escaped', '$unique_logo_name', '$status_value')";
            $result = mysqli_query($con, $query);
            
            if($result) {
                echo "<div class='alert alert-success'>✓ Brand added successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>✗ Error: " . mysqli_error($con) . "</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>✗ Error uploading file!</div>";
        }
    
    } else {
        echo "<div class='alert alert-warning'>⚠ Please select a logo!</div>";
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
                <h4 class="page-title">Add Brand</h4>
            </div>
    </div>     
    <!-- end page title -->

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Brand Information</h4>
                </div>
                <div class="card-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        
                        <div class="mb-3">
                            <label for="brand_name" class="form-label">Brand Name</label>
                            <input type="text" class="form-control" id="bname" name="bname" placeholder="Enter brand name" required>
                        </div>

                        <div class="mb-3">
                            <label for="brand_logo" class="form-label">Brand Logo</label>
                            <input type="file" class="form-control" id="logo" name="logo" accept="image/*" required>
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
