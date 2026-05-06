<!DOCTYPE html>
<html lang="en">
    <body>
    <?php
    use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require 'vendor/autoload.php'; // Composer
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';
$mail = new PHPMailer(true);

    session_start();
    include('../admin/connect.php');

    // Create table if not exists (setup_users code)
    $table_sql = "CREATE TABLE IF NOT EXISTS user_master (
        uid INT AUTO_INCREMENT PRIMARY KEY,
        fname VARCHAR(100) NOT NULL,
        lname VARCHAR(100) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        mobno VARCHAR(20),
        pass VARCHAR(255) NOT NULL,
        gender VARCHAR(20),
        status INT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    mysqli_query($con, $table_sql);

    $success_msg = '';
    $error_msg = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fname = trim($_POST['firstName'] ?? '');
        $lname = trim($_POST['lastName'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $mobno = trim($_POST['phone'] ?? '');
        $pass = $_POST['password'] ?? '';
        $gender = $_POST['gender'] ?? '';
    $otp=rand(999,9999);
        // Password confirm (client + server)
        $confirm_pass = $_POST['confirmPassword'] ?? '';
        if ($pass !== $confirm_pass) {
            $error_msg = 'Passwords do not match.';
        } elseif (empty($fname) || empty($lname) || empty($email) || empty($pass) || empty($gender) || empty($mobno)) {
            $error_msg = 'Please fill in all required fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_msg = 'Please enter a valid email address.';
        } elseif (strlen($pass) < 8) {
            $error_msg = 'Password must be at least 8 characters.';
        } else {
            // Check email exists
            $check = mysqli_prepare($con, "SELECT uid FROM user_master WHERE email = ?");
            mysqli_stmt_bind_param($check, "s", $email);
            mysqli_stmt_execute($check);
            if (mysqli_stmt_get_result($check)->num_rows > 0) {
                $error_msg = 'Email already registered.';
            } else {
$hashed_pass = $pass; // Plain text for visibility

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';  
    $mail->SMTPAuth   = true;
    $mail->Username   = 'ecommercepro1212@gmail.com';
    $mail->Password   = 'itbe bzrw jfhp dvqu'; // NOT your real password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Sender & receiver
    $mail->setFrom('ecommercepro1212@gmail.com', 'E');
    $mail->addAddress($email);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'OTP verification ';
    $mail->Body    = '<h1>Hello!</h1><p>This is OTP </p>'.$otp;

    $mail->send();
    echo 'Message sent successfully';
} catch (Exception $e) {
    echo "Mailer Error: {$mail->ErrorInfo}";
}
                $stmt = mysqli_prepare($con, "INSERT INTO user_master (fname, lname, email, mobno, pass, gender, status) VALUES (?, ?, ?, ?, ?, ?, 1)");
                mysqli_stmt_bind_param($stmt, "ssssss", $fname, $lname, $email, $mobno, $hashed_pass, $gender);
                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['user_id'] = mysqli_insert_id($con);
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_name'] = $fname . ' ' . $lname;
                    $success_msg = 'Registration successful! Redirecting...';
                    echo "<script>setTimeout(() => { window.location.href = 'login.php'; }, 1500);</script>";
                } else {
                    $error_msg = 'Registration failed: ' . mysqli_error($con);
                }
                mysqli_stmt_close($stmt);
            }
            mysqli_stmt_close($check);
        }
    }
    ?>
    <nav class="top-nav">
        <a href="login.php"><i class="fas fa-sign-in-alt"></i> Sign In</a>
        <a href="registration.php" class="active"><i class="fas fa-user-plus"></i> Sign Up</a>
        <a href="Home.php"><i class="fa-solid fa-house-user"></i> Home</a>
        <a href="tel:01234567898"><i class="fas fa-phone"></i> Call : 01234567898</a>
        <a href="mailto:info@dressify.com"><i class="fas fa-envelope"></i> info@elite-shoppy.com</a>
    </nav>
    <div class="modal-overlay">
        <div class="modal-card">
            <button class="close-btn" onclick="window.location.href='login.php'" title="Close">&times;</button>
            <div class="form-section">
                <h2 class="form-title">Sign Up <span>Now</span></h2>
                <?php if ($error_msg): ?>
                    <div class="alert error"><?php echo $error_msg; ?></div>
                <?php endif; ?>
                <?php if ($success_msg): ?>
                    <div class="alert success"><?php echo $success_msg; ?></div>
                <?php endif; ?>
                <form id="signupForm" method="POST" novalidate>
        <div class="fields-row">
    <div class="field-group">
        <input type="text" id="firstName" name="firstName" placeholder="First Name" value="<?php echo $_POST['firstName'] ?? ''; ?>" autocomplete="given-name">
    </div>

    <div class="field-group">
        <input type="text" id="lastName" name="lastName" placeholder="Last Name" value="<?php echo $_POST['lastName'] ?? ''; ?>" autocomplete="family-name">
    </div>
</div>
            <div class="field-group">
                <input type="email" id="email" name="email" placeholder="Email Address" value="<?php echo $_POST['email'] ?? ''; ?>" autocomplete="email">
            </div>
            <div class="field-group">
                <input type="tel" id="phone" name="phone" placeholder="Phone Number" value="<?php echo $_POST['phone'] ?? ''; ?>" autocomplete="tel">
            </div>
            <div class="field-group">
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" placeholder="Password" autocomplete="new-password">
                    <button type="button" class="pw-toggle" id="togglePw1"><i class="fas fa-eye"></i></button>
                </div>
            </div>
            <div class="field-group">
                <div class="password-wrapper">
                    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" autocomplete="new-password">
                    <button type="button" class="pw-toggle" id="togglePw2"><i class="fas fa-eye"></i></button>
                </div>
            </div>
            <div class="field-group">
                <div class="select-wrapper">
                    <select id="gender" name="gender">
                        <option value="" disabled selected>Gender</option>
                        <option value="female" <?php echo ($_POST['gender'] ?? '') == 'female' ? 'selected' : ''; ?>>Female</option>
                        <option value="male" <?php echo ($_POST['gender'] ?? '') == 'male' ? 'selected' : ''; ?>>Male</option>
                        <option value="other" <?php echo ($_POST['gender'] ?? '') == 'other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
            </div>
                    <div class="terms-row">
                        <input type="checkbox" id="terms" name="terms" required>
                        <label for="terms">I agree to the <a href="#">Terms & Conditions</a> and <a href="#">Privacy Policy</a> of Dressify</label>
                    </div>
                    <button type="submit" class="btn-signup">
                        <i class="fas fa-user-plus" style="margin-right:8px;"></i>Create Account
                    </button>
                </form>
                <div class="social-row">
                    <a href="#" class="social-btn fb" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-btn tw" title="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-btn ig" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-btn li" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
                <div class="signin-text">
                    Already have an account? <a href="login.php">Sign In</a>
                </div>
            </div>
            <div class="image-section">
                <img src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600&q=80" alt="Fashion Model" />
                <div class="image-brand">
                    <h3>Elite Shoppy</h3>
                    <p>Style · Rent · Repeat</p>
                </div>
            </div>
        </div>
    </div>
    <script>
        function setupToggle(btnId, inputId) {
            document.getElementById(btnId).addEventListener('click', function() {
                const input = document.getElementById(inputId);
                const isText = input.type === 'text';
                input.type = isText ? 'password' : 'text';
                this.querySelector('i').className = isText ? 'fas fa-eye' : 'fas fa-eye-slash';
            });
        }
        setupToggle('togglePw1', 'password');
        setupToggle('togglePw2', 'confirmPassword');
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            const pass = document.getElementById('password').value;
            const confirmPass = document.getElementById('confirmPassword').value;
            if (pass !== confirmPass) {
                e.preventDefault();
                const alertBox = document.getElementById('alertBox');
                alertBox.textContent = 'Passwords do not match.';
                alertBox.className = 'alert error';
                alertBox.style.display = 'block';
            }
        });
    </script>
</body>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Dressify</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { 
    font-family: 'Montserrat', sans-serif; 
    min-height: 100vh; 
    background: #1a1a1a; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    overflow: hidden; 
    position: relative; 
}
body::before {
    content: '';
    position: fixed;
    inset: 0;
    background: url('https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1400') center/cover no-repeat;
    filter: blur(8px) brightness(0.4);
    z-index: 0;
    transform: scale(1.05);
}

        .top-nav { height: 60px; position: fixed; top: 0; left: 0; right: 0; background: #111; display: flex; z-index: 10; }
        .top-nav a { flex: 1; color: #ccc; text-decoration: none; font-size: 13px; font-weight: 500; letter-spacing: 0.05em; padding: 14px 20px; display: flex; align-items: center; justify-content: center; gap: 8px; border-right: 1px solid #222; transition: background 0.2s, color 0.2s; }
        .top-nav a:last-child { border-right: none; }
        .top-nav a:hover, .top-nav a.active { background: #222; color: #fff; }
.modal-overlay {
    position: relative;
    z-index: 5;
    width: 100%;
    max-width: 800px;
    margin: 80px 20px 0 20px;
    animation: fadeInUp 0.5s ease forwards;
}
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .modal-card { background: #fff; border-radius: 0; display: flex; overflow: hidden; box-shadow: 0 30px 80px rgba(0,0,0,0.6); min-height: auto; }
        .close-btn { position: absolute; top: 12px; right: 12px; width: 30px; height: 30px; background: none; border: none; font-size: 20px; color: #999; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 10; transition: color 0.2s; }
        .close-btn:hover { color: #333; }
        .form-section { flex: 1; padding: 40px 45px; display: flex; flex-direction: column; justify-content: center; position: relative; }
        .form-title { font-family: 'Montserrat', sans-serif; font-size: 22px; font-weight: 700; letter-spacing: 0.12em; color: #111; margin-bottom: 28px; text-transform: uppercase; }
        .form-title span { font-weight: 300; color: #555; }
    .field-group { margin-bottom: 22px; }
    .fields-row {
    display: flex;
    gap: 15px;
}

.fields-row .field-group {
    flex: 1;
}
    .fields-grid { }
        .field-group { margin-bottom: 20px; }

        .field-group input, .field-group select { width: 100%; border: none; border-bottom: 1.5px solid #ccc; padding: 10px 0; font-family: 'Montserrat', sans-serif; font-size: 13px; color: #333; background: transparent; outline: none; transition: border-color 0.3s; letter-spacing: 0.03em; appearance: none; }
        .field-group input::placeholder { color: #aaa; font-size: 13px; }
        .field-group input:focus, .field-group select:focus { border-bottom-color: #111; }
        .field-group select { cursor: pointer; color: #aaa; }
        .field-group select.filled { color: #333; }
        .select-wrapper { position: relative; }
        .select-wrapper::after { content: '\f107'; font-family: 'Font Awesome 6 Free'; font-weight: 900; position: absolute; right: 0; top: 50%; transform: translateY(-50%); color: #aaa; pointer-events: none; font-size: 12px; }
        .password-wrapper { position: relative; }
        .password-wrapper input { padding-right: 30px; }
        .pw-toggle { position: absolute; right: 0; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #aaa; font-size: 13px; transition: color 0.2s; }
        .pw-toggle:hover { color: #333; }
        .terms-row { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 22px; margin-top: 4px; }
        .terms-row input[type="checkbox"] { width: 15px; height: 15px; margin-top: 2px; accent-color: #111; flex-shrink: 0; cursor: pointer; }
        .terms-row label { font-size: 11px; color: #888; line-height: 1.5; letter-spacing: 0.02em; }
        .terms-row label a { color: #111; font-weight: 600; text-decoration: none; }
        .terms-row label a:hover { text-decoration: underline; }
        .btn-signup { width: 100%; padding: 14px; background: #111; color: #fff; border: none; font-family: 'Montserrat', sans-serif; font-size: 13px; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; cursor: pointer; transition: background 0.3s; margin-bottom: 22px; }
        .btn-signup:hover, .btn-signup:disabled { background: #333; }
        .social-row { display: flex; gap: 10px; margin-bottom: 20px; }
        .social-btn { width: 40px; height: 40px; border: none; border-radius: 4px; color: #fff; font-size: 15px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: opacity 0.2s, transform 0.2s; text-decoration: none; }
        .social-btn:hover { opacity: 0.85; transform: translateY(-2px); }
        .social-btn.fb { background: #1877f2; }
        .social-btn.tw { background: #1da1f2; }
        .social-btn.ig { background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); }
        .social-btn.li { background: #0a66c2; }
        .signin-text { font-size: 12px; color: #888; letter-spacing: 0.03em; }
        .signin-text a { color: #111; font-weight: 600; text-decoration: none; }
        .signin-text a:hover { text-decoration: underline; }
        .alert { padding: 10px 14px; font-size: 12px; margin-bottom: 14px; display: none; grid-column: 1 / -1; }
        .alert.error { background: #fff0f0; border-left: 3px solid #e53e3e; color: #c53030; }
        .alert.success { background: #f0fff4; border-left: 3px solid #38a169; color: #2f855a; }
        .image-section { width: 260px; flex-shrink: 0; overflow: hidden; position: relative; }
        .image-section img { width: 100%; height: 100%; object-fit: cover; object-position: top center; display: block; }
        .image-brand { position: absolute; bottom: 20px; left: 0; right: 0; text-align: center; color: white; text-shadow: 0 2px 8px rgba(0,0,0,0.5); }
        .image-brand h3 { font-family: 'Cormorant Garamond', serif; font-size: 28px; font-weight: 700; letter-spacing: 0.1em; }
        .image-brand p { font-size: 10px; letter-spacing: 0.2em; opacity: 0.85; text-transform: uppercase; }
        @media (max-width: 640px) { .image-section { display: none; } .form-section { padding: 35px 25px; } .fields-grid { grid-template-columns: 1fr; } .field-group.full { grid-column: 1; } }
    </style>
</head>

</html>
