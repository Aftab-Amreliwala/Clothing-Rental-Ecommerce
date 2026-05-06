<?php
include('connect.php');

echo "<h3>Checking Tables...</h3>";

// Check category_master
echo "<h4>category_master</h4>";
$cols = mysqli_query($con, "SHOW COLUMNS FROM category_master");
echo "<ul>";
while($c = mysqli_fetch_assoc($cols)) {
    echo "<li>" . $c['Field'] . " - " . $c['Type'] . "</li>";
}
echo "</ul>";

// Check brand_master
echo "<h4>brand_master</h4>";
$cols = mysqli_query($con, "SHOW COLUMNS FROM brand_master");
echo "<ul>";
while($c = mysqli_fetch_assoc($cols)) {
    echo "<li>" . $c['Field'] . " - " . $c['Type'] . "</li>";
}
echo "</ul>";

// Check subcategory_master
echo "<h4>subcategory_master</h4>";
$cols = mysqli_query($con, "SHOW COLUMNS FROM subcategory_master");
echo "<ul>";
while($c = mysqli_fetch_assoc($cols)) {
    echo "<li>" . $c['Field'] . " - " . $c['Type'] . "</li>";
}
echo "</ul>";

// Check product_master
echo "<h4>product_master</h4>";
$cols = mysqli_query($con, "SHOW COLUMNS FROM product_master");
echo "<ul>";
while($c = mysqli_fetch_assoc($cols)) {
    echo "<li>" . $c['Field'] . " - " . $c['Type'] . "</li>";
}
echo "</ul>";

mysqli_close($con);
?>

