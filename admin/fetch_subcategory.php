<?php

include("connect.php");

$cid=$_POST['cid'];

$query=mysqli_query($con,"SELECT * FROM subcategory_master WHERE cid='$cid' AND status=1");

echo "<option value=''>Select Subcategory</option>";

while($row=mysqli_fetch_assoc($query))
{
echo "<option value='".$row['sid']."'>".$row['sname']."</option>";
}

?>