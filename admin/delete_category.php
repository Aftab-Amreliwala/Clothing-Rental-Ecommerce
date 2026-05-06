<?php
include('hhh.php'); 
include("connect.php");

// Check if ID is provided and valid
if (isset($_GET["id"]) && !empty($_GET["id"]) && is_numeric($_GET["id"])) {
    $id = intval($_GET['id']); // Convert to integer for safety
    
    // First, get the photo name to delete the file
    $photo_query = mysqli_query($con, "SELECT photo FROM category_master WHERE cid = $id");
    
    if ($photo_query && mysqli_num_rows($photo_query) > 0) {
        $photo_row = mysqli_fetch_assoc($photo_query);
        $photo_name = $photo_row['photo'];
        
        // Delete the record from database
        $q = mysqli_query($con, "DELETE FROM category_master WHERE cid = $id");
        
        if ($q) {
            if (mysqli_affected_rows($con) > 0) {
                // Only delete the photo file if NO OTHER category is using the same image
                if (!empty($photo_name)) {
                    // Check if any other category is using the same photo
                    $check_photo = mysqli_query($con, "SELECT COUNT(*) as count FROM category_master WHERE photo = '$photo_name'");
                    $check_result = mysqli_fetch_assoc($check_photo);
                    
                    // If no other category is using this photo, then delete it
                    if ($check_result['count'] == 0 && file_exists("./images/" . $photo_name)) {
                        unlink("./images/" . $photo_name);
                    }
                }
                
                echo "<script>
                        alert('Category deleted successfully!');
                        window.location.href = 'view_category.php';
                      </script>";
            } else {
                echo "<script>
                        alert('No record found to delete!');
                        window.location.href = 'view_category.php';
                      </script>";
            }
        } else {
            echo "<script>
                    alert('Error deleting record: " . addslashes(mysqli_error($con)) . "');
                    window.location.href = 'view.php';
                  </script>";
        }
    } else {
        echo "<script>
                alert('Category not found!');
                window.location.href = 'view_category.php.php';
              </script>";
    }
} else {
    echo "<script>
            alert('Invalid category ID!');
            window.location.href = 'view_category.php.php';
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
    <title>Delete Category</title>
</head>
<body>
    <div style="text-align: center; margin-top: 100px;">
        <h3>Processing delete request...</h3>
        <p>Please wait while we redirect you.</p>
    </div>
</body>
</html>