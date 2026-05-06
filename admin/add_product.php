<?php
include('hhh.php');
include("connect.php");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if(isset($_POST["btnins"]))
{
    $cid = isset($_POST['ddlcategory']) ? intval($_POST['ddlcategory']) : 0;
    $sid = isset($_POST['dlcategory']) ? intval($_POST['dlcategory']) : 0;
    $bid = isset($_POST['ddlbrand']) ? intval($_POST['ddlbrand']) : 0;
    $pname = isset($_POST["productName"]) ? trim($_POST["productName"]) : '';
    $description = isset($_POST["productDesc"]) ? trim($_POST["productDesc"]) : '';
    $qty = isset($_POST["productQty"]) ? intval($_POST["productQty"]) : 0;
    $price = isset($_POST["productprice"]) ? floatval($_POST["productprice"]) : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : 'Active';
    
    if($status == 'Active') {
        $status_value = 1;
    } else {
        $status_value = 0;
    }
    
    $photo = "";
    if(isset($_FILES["productPhoto"]) && $_FILES["productPhoto"]["error"] == 0) {
        $original_name = $_FILES["productPhoto"]['name'];
        $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
        $photo = time() . '_' . uniqid() . '.' . $file_extension;
        $dst = './images/' . $photo;
        
        if (!file_exists('./images')) {
            mkdir('./images', 0777, true);
        }
    }
    
    if($cid <= 0 || $sid <= 0 || $bid <= 0 || empty($pname) || empty($description) || $qty <= 0 || $price <= 0) {
        echo "<div class='alert alert-warning'>⚠ Please fill all fields correctly! Select Category, Subcategory, and Brand from dropdowns.</div>";
    } else {
        
        // Use correct column names based on database structure: scid, pdesc, rate
        $query = "INSERT INTO product_master (bid, sid, pname, pdesc, qty, rate, photo, status) 
                  VALUES ($bid, $sid, '$pname', '$description', '$qty', '$price', '$photo', '$status_value')";
        
        $q = mysqli_query($con, $query);
        
        if($q)
        {
            if(!empty($photo) && isset($_FILES["productPhoto"])) {
                if(move_uploaded_file($_FILES["productPhoto"]['tmp_name'], $dst)) {
                    echo "<div class='alert alert-success'>✓ Product added successfully!</div>";
                } else {
                    echo "<div class='alert alert-warning'>⚠ Product added but image upload failed!</div>";
                }
            } else {
                echo "<div class='alert alert-success'>✓ Product added successfully!</div>";
            }
        }    
        else    
        {
            echo "<div class='alert alert-danger'>✗ Error: " . mysqli_error($con) . "</div>";
        }
    }
}
?>

<div class="container-fluid">
<div class="content-page">
<div class="content">

<div class="row">
<div class="col-12">
<div class="page-title-box">
<h4 class="page-title">Add Product</h4>
</div>
</div>
</div>

<div class="row justify-content-center">
<div class="col-lg-8">
<div class="card">

<div class="card-header">
<h4 class="header-title">Product Information</h4>
</div>

<div class="card-body">

<form action="" method="POST" enctype="multipart/form-data">

<div class="row">

<div class="col-md-6">
<div class="mb-3">

<label class="form-label">Select Category</label>

<select class="form-control" id="ddlcategory" name="ddlcategory" required>

<option value="">-- Select Category --</option>

<?php
$cat_query = mysqli_query($con, "SELECT * FROM category_master WHERE status = 1 ORDER BY cname");

while($cat_row = mysqli_fetch_assoc($cat_query)) {

echo "<option value='".$cat_row['cid']."'>".$cat_row['cname']."</option>";

}
?>

</select>

</div>
</div>

<div class="col-md-6">
<div class="mb-3">

<label class="form-label">Select Sub Category</label>

<select class="form-control" id="dlcategory" name="dlcategory">

<option value="">-- Select Sub Category --</option>

</select>

</div>
</div>

</div>

<div class="row">

<div class="col-md-6">
<div class="mb-3">

<label class="form-label">Select Brand</label>

<select class="form-control" name="ddlbrand">

<option value="">-- Select Brand --</option>

<?php
$brand_query = mysqli_query($con, "SELECT * FROM brand_master WHERE status = 1 ORDER BY bname");

while($brand_row = mysqli_fetch_assoc($brand_query)) {

echo "<option value='".$brand_row['bid']."'>".$brand_row['bname']."</option>";

}
?>

</select>

</div>
</div>

<div class="col-md-6">
</div>
</div>

<div class="mb-3">

<label class="form-label">Product Name</label>

<input type="text" class="form-control" name="productName" required>

</div>

<div class="mb-3">

<label class="form-label">Product Description</label>

<textarea class="form-control" name="productDesc" rows="4" required></textarea>

</div>

<div class="row">

<div class="col-md-6">
<div class="mb-3">

<label class="form-label">Quantity</label>

<input type="number" class="form-control" name="productQty" required>

</div>
</div>

<div class="col-md-6">
<div class="mb-3">

<label class="form-label">price</label>

<input type="number" class="form-control" name="productprice" required>

</div>
</div>

</div>

<div class="mb-3">

<label class="form-label">Product Photo</label>

<input type="file" class="form-control" name="productPhoto" required>

</div>

<div class="mb-3">

<label class="form-label">Status</label>

<select class="form-control" name="status">

<option value="Active">Active</option>
<option value="Inactive">Inactive</option>

</select>

</div>

<div class="text-center">

<button type="submit" name="btnins" class="btn btn-primary">
Add Product
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

</div>
</div>
</div>

<!-- AJAX SCRIPT -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

$("#ddlcategory").change(function(){

var cid=$(this).val();

$.ajax({

url:"fetch_subcategory.php",

method:"POST",

data:{cid:cid},

success:function(data)
{
$("#dlcategory").html(data);
}

});

});

</script>

<?php
include('fff.php');
?>