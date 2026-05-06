<html>
<body>
    <link rel="stylesheet" type="text/css"
        href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css"
        href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
    <style type="text/css">
    body {
        background: #f2f2f2;
    }

    .payment {
        border: 1px solid #f2f2f2;
        height: 280px;
        border-radius: 20px;
        background: #fff;
    }

    .payment_header {
        background: #7AB3E1;
        padding: 20px;
        border-radius: 20px 20px 0px 0px;

    }

    .check {
        margin: 0px auto;
        width: 50px;
        height: 50px;
        border-radius: 100%;
        background: #fff;
        text-align: center;
    }

    .check i {
        vertical-align: middle;
        line-height: 50px;
        font-size: 30px;
    }

    .content {
        text-align: center;
    }

    .content h1 {
        font-size: 25px;
        padding-top: 25px;
    }

    .content a {
        width: 200px;
        height: 35px;
        color: #fff;
        border-radius: 10px;
        padding: 5px 10px;
        background: #007bff;
        transition: all ease-in-out 0.3s;
    }

    .content a:hover {
        text-decoration: none;
        background: #7AB3E1;
    }
    </style>
    <?php
    session_start();
      include('connect.php');
      $uid=$_SESSION['user_id'];
      $q=mysqli_query($conn,"select * from order_master where user_id=$uid");
      $row=mysqli_fetch_array($q);
      $oid=$row[0];
      $_SESSION['oid']=$oid;
      // $uname=$_SESSION['uname'];
      // $uname=$_GET['uname'];
      if(isset($_POST['b1']))
      {
          $oid=$row[0];
          $uid=$row[1];
          $uname=$row[2];
          $address=$row[3];
          $zipcode=$row[4];
          $phone=$row[5];
          $email=$row[6];
          $subtotal=$row[7];
          $total=$row[8];
          $odate=$row[9];
          $q1=mysqli_query($conn,"insert into order_history values($oid,$uid,'$uname','$address','$zipcode','$phone','$email',$subtotal,$total,'$odate',1)");
       mysqli_query($conn,"update addtocart set status=1 where uid=$uid");
       mysqli_query($conn,"update addtocart1 set status=1 where uid=$uid");
       header('location:../home.php');
      }
    ?>
    <div class="container">
        <div class="row">
            <div class="col-md-6 mx-auto mt-5">
                <div class="payment">
                    <div class="payment_header">
                        <div class="check"><i class="fa fa-check" aria-hidden="true"></i></div>
                    </div>
                    <form method="POST">
                    <div class="content">
                        <h1>Payment Success !</h1>
                        <p>Thank you by,</p>
                        <div>
                            <p>Team InfinitexAgro</p>
                        <form method=post>
                        <h6>
                        <a href="../home.php" type=submit name="b1" >    Go to Home</a>
</form>    
                    </div> 
                        </a>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
