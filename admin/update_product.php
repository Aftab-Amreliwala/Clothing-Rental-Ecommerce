<?php
include('hhh.php');
include("connect.php");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get product ID from URL
$pid = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($pid <= 0) {
    die("<div class='alert alert-danger'>Invalid product ID.</div>");
}

// Fetch existing product data
$fetch_query = mysqli_query($con, "SELECT * FROM product_master WHERE pid = $pid");
if (!$fetch_query || mysqli_num_rows($fetch_query) == 0) {
    die("<div class='alert alert-danger'>Product not found.</div>");
}
$product = mysqli_fetch_assoc($fetch_query);

// Handle form submission
if (isset($_POST["btnupdate"])) {
    $cid         = mysqli_real_escape_string($con, $_POST['ddlcategory']);
    $sid         = mysqli_real_escape_string($con, $_POST['dlcategory']);
    $bid         = mysqli_real_escape_string($con, $_POST['ddlbrand']);
    $pname       = mysqli_real_escape_string($con, $_POST["productName"]);
    $description = mysqli_real_escape_string($con, $_POST["productDesc"]);
    $qty         = mysqli_real_escape_string($con, $_POST["productQty"]);
    $price       = mysqli_real_escape_string($con, $_POST["productprice"]);
    $status      = isset($_POST['status']) ? $_POST['status'] : 'Active';
    $status_value = ($status == 'Active') ? 1 : 0;

    // Handle photo upload
    $photo = $product['photo']; // keep existing photo by default

    if (isset($_FILES["productPhoto"]) && $_FILES["productPhoto"]["error"] == 0) {
        $original_name  = $_FILES["productPhoto"]['name'];
        $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
        $new_photo      = time() . '_' . uniqid() . '.' . $file_extension;
        $dst            = './images/' . $new_photo;

        if (!file_exists('./images')) {
            mkdir('./images', 0777, true);
        }

        if (move_uploaded_file($_FILES["productPhoto"]['tmp_name'], $dst)) {
            // Delete old photo if exists
            if (!empty($product['photo']) && file_exists('./images/' . $product['photo'])) {
                unlink('./images/' . $product['photo']);
            }
            $photo = $new_photo;
        } else {
            echo "<div class='alert alert-warning'>⚠ Image upload failed. Keeping existing image.</div>";
        }
    }

    if (empty($pname) || empty($description) || empty($qty) || empty($price)) {
        echo "<div class='alert alert-warning'>⚠ All fields are required!</div>";
    } else {
        $query = "UPDATE product_master SET 
                    bid = $bid,
                    sid = $sid,
                    pname = '$pname',
                    description = '$description',
                    qty = '$qty',
                    price = '$price',
                    photo = '$photo',
                    status = '$status_value'
                  WHERE pid = $pid";

        $q = mysqli_query($con, $query);

        if ($q) {
            // Refresh product data after update
            $fetch_query = mysqli_query($con, "SELECT * FROM product_master WHERE pid = $pid");
            $product = mysqli_fetch_assoc($fetch_query);
            echo "<div class='alert alert-success'>✓ Product updated successfully!</div>";
        } else {
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
<div class="page-title-box d-flex justify-content-between align-items-center">
    <h4 class="page-title">Update Product</h4>
    <a href="view_product.php" class="btn btn-secondary btn-sm">
        <i class="ri-arrow-left-line me-1"></i> Back to Products
    </a>
</div>
</div>
</div>

<div class="row justify-content-center">
<div class="col-lg-8">
<div class="card">

<div class="card-header">
<h4 class="header-title">Edit Product Information</h4>
</div>

<div class="card-body">

<form action="" method="POST" enctype="multipart/form-data">

<div class="row">

<!-- Category -->
<div class="col-md-6">
<div class="mb-3">
<label class="form-label">Select Category</label>
<select class="form-control" id="ddlcategory" name="ddlcategory" required>
<option value="">-- Select Category --</option>
<?php
$cat_query = mysqli_query($con, "SELECT * FROM category_master WHERE status = 1 ORDER BY cname");
while ($cat_row = mysqli_fetch_assoc($cat_query)) {
    $selected = ($cat_row['cid'] == $product['cid']) ? "selected" : "";
    echo "<option value='" . $cat_row['cid'] . "' $selected>" . htmlspecialchars($cat_row['cname']) . "</option>";
}
?>
</select>
</div>
</div>

<!-- Sub Category -->
<div class="col-md-6">
<div class="mb-3">
<label class="form-label">Select Sub Category</label>
<select class="form-control" id="dlcategory" name="dlcategory">
<option value="">-- Select Sub Category --</option>
<?php
// Pre-load subcategories for the current category
if (!empty($product['sid'])) {
    $sub_query = mysqli_query($con, "SELECT * FROM subcategory_master WHERE cid = " . intval($product['cid']) . " AND status = 1 ORDER BY sname");
    if ($sub_query) {
        while ($sub_row = mysqli_fetch_assoc($sub_query)) {
            $selected = ($sub_row['sid'] == $product['sid']) ? "selected" : "";
            echo "<option value='" . $sub_row['sid'] . "' $selected>" . htmlspecialchars($sub_row['sname']) . "</option>";
        }
    }
}
?>
</select>
</div>
</div>

</div>

<div class="row">

<!-- Brand -->
<div class="col-md-6">
<div class="mb-3">
<label class="form-label">Select Brand</label>
<select class="form-control" name="ddlbrand">
<option value="">-- Select Brand --</option>
<?php
$brand_query = mysqli_query($con, "SELECT * FROM brand_master WHERE status = 1 ORDER BY bname");
while ($brand_row = mysqli_fetch_assoc($brand_query)) {
    $selected = ($brand_row['bid'] == $product['bid']) ? "selected" : "";
    echo "<option value='" . $brand_row['bid'] . "' $selected>" . htmlspecialchars($brand_row['bname']) . "</option>";
}
?>
</select>
</div>
</div>

<div class="col-md-6"></div>
</div>

<!-- Product Name -->
<div class="mb-3">
<label class="form-label">Product Name</label>
<input type="text" class="form-control" name="productName"
       value="<?php echo htmlspecialchars($product['pname']); ?>" required>
</div>

<!-- Description -->
<div class="mb-3">
<label class="form-label">Product Description</label>
<textarea class="form-control" name="productDesc" rows="4" required><?php echo htmlspecialchars($product['description']); ?></textarea>
</div>

<div class="row">

<!-- Qty -->
<div class="col-md-6">
<div class="mb-3">
<label class="form-label">Quantity</label>
<input type="number" class="form-control" name="productQty"
       value="<?php echo htmlspecialchars($product['qty']); ?>" required>
</div>
</div>

<!-- Price -->
<div class="col-md-6">
<div class="mb-3">
<label class="form-label">Price</label>
<input type="number" class="form-control" name="productprice"
       value="<?php echo htmlspecialchars($product['price']); ?>" required>
</div>
</div>

</div>

<!-- Photo -->
<div class="mb-3">
<label class="form-label">Product Photo</label>
<?php if (!empty($product['photo'])): ?>
    <div class="mb-2">
        <img src="images/<?php echo htmlspecialchars($product['photo']); ?>"
             alt="Current Product Image"
             style="width: 80px; height: 80px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd;">
        <small class="text-muted ms-2">Current photo — upload a new one to replace it</small>
    </div>
<?php endif; ?>
<input type="file" class="form-control" name="productPhoto">
</div>

<!-- Status -->
<div class="mb-3">
<label class="form-label">Status</label>
<select class="form-control" name="status">
    <option value="Active"  <?php echo ($product['status'] == 1) ? 'selected' : ''; ?>>Active</option>
    <option value="Inactive" <?php echo ($product['status'] == 0) ? 'selected' : ''; ?>>Inactive</option>
</select>
</div>

<div class="text-center">
<button type="submit" name="btnupdate" class="btn btn-primary">
    <i class="ri-save-line me-1"></i> Update Product
</button>
<a href="view_product.php" class="btn btn-secondary ms-2">Cancel</a>
</div>

</form>

</div>
</div>
</div>
</div>

</div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// On category change — reload subcategories
$("#ddlcategory").change(function () {
    var cid = $(this).val();
    $.ajax({
        url: "fetch_subcategory.php",
        method: "POST",
        data: { cid: cid },
        success: function (data) {
            $("#dlcategory").html(data);
        }
    });
});
</script>

<?php include('fff.php'); ?>