<?php
session_start();
include 'connect.php'; // DB connection

if(isset($_POST['login']))
{
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($con,"SELECT * FROM admin_master 
        WHERE username='$username' AND password='$password'");

    if(mysqli_num_rows($query) > 0)
    {
        $row = mysqli_fetch_assoc($query);
        $_SESSION['admin_id'] = $row['admin_id'];

        header("location:dashboard.php");
    }
    else
    {
        echo "<script>alert('Invalid Login');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Login</title>

<style>
body {
    font-family: Arial;
    background: linear-gradient(135deg,#667eea,#764ba2);
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.login-box {
    background: #fff;
    padding: 40px;
    width: 350px;
    border-radius: 10px;
    box-shadow: 0px 10px 25px rgba(0,0,0,0.2);
}

.login-box h2 {
    text-align: center;
    margin-bottom: 20px;
}

.input-box {
    margin-bottom: 15px;
}

.input-box input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

button {
    width: 100%;
    padding: 10px;
    background: #667eea;
    border: none;
    color: #fff;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
}

button:hover {
    background: #5a67d8;
}
</style>
</head>

<body>

<div class="login-box">
    <h2>Admin Login</h2>

    <form method="post">
        <div class="input-box">
            <input type="text" name="username" placeholder="Username" required>
        </div>

        <div class="input-box">
            <input type="password" name="password" placeholder="Password" required>
        </div>

        <button type="submit" name="login">Login</button>
    </form>
</div>

</body>
</html>