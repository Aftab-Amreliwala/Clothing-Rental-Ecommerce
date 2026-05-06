<?php
include('hhh.php');
include("connect.php");

// Step 1: Check if form is submitted
if(isset($_POST["submit"])) {
    
    // Step 2: Get data from form
    $cname = $_POST["cname"];
    $photo_name = $_FILES["photo"]['name'];
    $status = $_POST["status"];
    
    // Step 3: Convert status to number (1 = Active, 0 = Inactive)
    if($status == 'Active') {
        $status_value = 1;
    } else {
        $status_value = 0;
    }
    
    // Step 4: Check if photo is uploaded
    if(!empty($photo_name)) {
        
        // Create unique name for photo
        $unique_photo_name = time() . '_' . $photo_name;
        
        // Where to save the photo
        $save_path = './images/' . $unique_photo_name;
        
        // Step 5: Save to database (escape special characters to prevent SQL errors)
        $cname_escaped = mysqli_real_escape_string($con, $cname);
        $query = "INSERT INTO category_master (cname, photo, status) VALUES ('$cname_escaped', '$unique_photo_name', '$status_value')";
        $result = mysqli_query($con, $query);
        
        // Step 6: If database save successful, then upload photo
        if($result) {
            move_uploaded_file($_FILES["photo"]['tmp_name'], $save_path);
            echo "<div class='alert alert-success'>✓ Category added successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>✗ Error: " . mysqli_error($con) . "</div>";
        }
        
    } else {
        // No photo uploaded
        echo "<div class='alert alert-warning'>⚠ Please select a photo!</div>";
    }
}
?>

<!-- HTML FORM -->
<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Add Category</h4>
                    </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-3">Category Information</h4>

                            <form action="add_category.php" method="POST" enctype="multipart/form-data">
                                
                                <!-- Category Name Input -->
                                <div class="mb-3">
                                    <label class="form-label">Category Name</label>
                                    <input type="text" name="cname" class="form-control" placeholder="Enter category name" required>
                                </div>

                                <!-- Photo Upload -->
                                <div class="mb-3">
                                    <label class="form-label">Category Photo</label>
                                    <input type="file" name="photo" class="form-control" accept="image/*" required>
                                    <small class="text-muted">Upload JPG, PNG, or GIF</small>
                                </div>

                                <!-- Status Dropdown -->
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>

                                <!-- Buttons -->
                                <div class="text-center">
                                    <button type="submit" name="submit" class="btn btn-primary">
                                        Save Category
                                    </button>
                                    <button type="reset" class="btn btn-secondary">
                                        Reset
                                    </button>
                                </div>

                            </form>

                        </div>
                </div>

        </div>
</div>

<?php include('fff.php'); ?>
