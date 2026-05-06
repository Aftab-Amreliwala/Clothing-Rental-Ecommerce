<?php
include('hhh.php'); 
include("connect.php");

// Check if ID is provided and valid
if (isset($_GET["id"]) && !empty($_GET["id"]) && is_numeric($_GET["id"])) {
    $id = intval($_GET['id']); // Convert to integer for safety
    
    // First, get the logo name to delete the file
    $logo_query = mysqli_query($con, "SELECT logo FROM brand_master WHERE bid = $id");
    
    if ($logo_query && mysqli_num_rows($logo_query) > 0) {
        $logo_row = mysqli_fetch_assoc($logo_query);
        $logo_name = $logo_row['logo'];
        
        // Delete the record from database
        $q = mysqli_query($con, "DELETE FROM brand_master WHERE bid = $id");
        
        if ($q) {
            if (mysqli_affected_rows($con) > 0) {
                // Only delete the logo file if NO OTHER brand is using the same image
                if (!empty($logo_name)) {
                    // Check if any other brand is using the same logo
                    $check_logo = mysqli_query($con, "SELECT COUNT(*) as count FROM brand_master WHERE logo = '$logo_name'");
                    $check_result = mysqli_fetch_assoc($check_logo);
                    
                    // If no other brand is using this logo, then delete it
                    if ($check_result['count'] == 0 && file_exists("./images/" . $logo_name)) {
                        unlink("./images/" . $logo_name);
                    }
                }
                
                echo "<script>
                        alert('Brand deleted successfully!');
                        window.location.href = 'view_brand.php';
                      </script>";
            } else {
                echo "<script>
                        alert('No record found to delete!');
                        window.location.href = 'view_brand.php';
                      </script>";
            }
        } else {
            echo "<script>
                    alert('Error deleting record: " . addslashes(mysqli_error($con)) . "');
                    window.location.href = 'view_brand.php';
                  </script>";
        }
    } else {
        echo "<script>
                alert('Brand not found!');
                window.location.href = 'view_brand.php';
              </script>";
    }
} else {
    echo "<script>
            alert('Invalid brand ID!');
            window.location.href = 'view_brand.php';
          </script>";
}

// Close connection
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Brand</title>
</head>
<body>
    <div style="text-align: center; margin-top: 100px;">
        <h3>Processing delete request...</h3>
        <p>Please wait while we redirect you.</p>
    </div>
</body>
</html>

