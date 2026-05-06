<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dressify</title>
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

        .top-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #111;
            display: flex;
            z-index: 10;
        }

        .top-nav a {
            flex: 1;
            color: #ccc;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            letter-spacing: 0.05em;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-right: 1px solid #222;
            transition: background 0.2s, color 0.2s;
        }

        .top-nav a:last-child { border-right: none; }
        .top-nav a:hover, .top-nav a.active { background: #222; color: #fff; }

        .modal-overlay {
            position: relative;
            z-index: 5;
            width: 100%;
            max-width: 800px;
            margin: 0 20px;
            animation: fadeInUp 0.5s ease forwards;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-card {
            background: #fff;
            border-radius: 0;
            display: flex;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0,0,0,0.6);
            min-height: 480px;
        }

        .close-btn {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 30px;
            height: 30px;
            background: none;
            border: none;
            font-size: 20px;
            color: #999;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            transition: color 0.2s;
        }
        .close-btn:hover { color: #333; }

        .form-section {
            flex: 1;
            padding: 50px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .form-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.12em;
            color: #111;
            margin-bottom: 35px;
            text-transform: uppercase;
        }

        .form-title span {
            font-weight: 300;
            color: #555;
        }

        .field-group {
            margin-bottom: 22px;
        }

        .field-group input {
            width: 100%;
            border: none;
            border-bottom: 1.5px solid #ccc;
            padding: 10px 0;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            color: #333;
            background: transparent;
            outline: none;
            transition: border-color 0.3s;
            letter-spacing: 0.03em;
        }

        .field-group input::placeholder { color: #aaa; font-size: 13px; }
        .field-group input:focus { border-bottom-color: #111; }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input { padding-right: 30px; }

        .pw-toggle {
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #aaa;
            font-size: 13px;
            transition: color 0.2s;
        }
        .pw-toggle:hover { color: #333; }

        .forgot-link {
            text-align: right;
            margin-top: -8px;
            margin-bottom: 28px;
        }
        .forgot-link a {
            font-size: 11px;
            color: #888;
            text-decoration: none;
            letter-spacing: 0.04em;
            transition: color 0.2s;
        }
        .forgot-link a:hover { color: #111; }

        .btn-signin {
            width: 100%;
            padding: 14px;
            background: #111;
            color: #fff;
            border: none;
            font-family: 'Montserrat', sans-serif;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            cursor: pointer;
            transition: background 0.3s;
            margin-bottom: 28px;
        }
        .btn-signin:hover:not(:disabled) { background: #333; }
        .btn-signin:disabled { opacity: 0.6; cursor: not-allowed; }

        .social-row {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
        }

        .social-btn {
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 4px;
            color: #fff;
            font-size: 15px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.2s, transform 0.2s;
            text-decoration: none;
        }
        .social-btn:hover { opacity: 0.85; transform: translateY(-2px); }
        .social-btn.fb { background: #1877f2; }
        .social-btn.tw { background: #1da1f2; }
        .social-btn.ig { background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); }
        .social-btn.li { background: #0a66c2; }

        .signup-text {
            font-size: 12px;
            color: #888;
            letter-spacing: 0.03em;
        }
        .signup-text a { color: #111; font-weight: 600; text-decoration: none; }
        .signup-text a:hover { text-decoration: underline; }

        .image-section {
            width: 280px;
            flex-shrink: 0;
            overflow: hidden;
            position: relative;
        }

        .image-section img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top center;
            display: block;
        }

        .image-brand {
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            color: white;
            text-shadow: 0 2px 8px rgba(0,0,0,0.5);
        }
        .image-brand h3 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 0.1em;
        }
        .image-brand p {
            font-size: 10px;
            letter-spacing: 0.2em;
            opacity: 0.85;
            text-transform: uppercase;
        }

        .alert {
            padding: 10px 14px;
            border-radius: 2px;
            font-size: 12px;
            margin-bottom: 16px;
            display: none;
        }
        .alert.error { background: #fff0f0; border-left: 3px solid #e53e3e; color: #c53030; }
        .alert.success { background: #f0fff4; border-left: 3px solid #38a169; color: #2f855a; }

        @media (max-width: 640px) {
            .image-section { display: none; }
            .form-section { padding: 40px 30px; }
        }
    </style>
</head>
<body>

    <nav class="top-nav">
        <a href="login.php" class="active"><i class="fas fa-sign-in-alt"></i> Sign In</a>
        <a href="registration.php"><i class="fas fa-user-plus"></i> Sign Up</a>
        <a href="Home.php"><i class="fa-solid fa-house-user"></i> Home</a>
        <a href="tel:01234567898"><i class="fas fa-phone"></i> Call : 01234567898</a>
        <a href="mailto:info@dressify.com"><i class="fas fa-envelope"></i> info@elite-shoppy.com</a>
    </nav>

    <div class="modal-overlay">
        <div class="modal-card">
            <button class="close-btn" onclick="window.history.back()" title="Close">&times;</button>

            <div class="form-section">
                <h2 class="form-title">Sign In <span>Now</span></h2>

                <div class="alert" id="alertBox"></div>

                <form id="loginForm" novalidate>
                    <div class="field-group">
                        <input type="email" id="email" name="email" placeholder="Email" required autocomplete="email">
                    </div>

                    <div class="field-group">
                        <div class="password-wrapper">
                            <input type="password" id="password" name="password" placeholder="Password" required autocomplete="current-password">
                            <button type="button" class="pw-toggle" id="togglePw">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="forgot-link">
                        <a href="#">Forgot Password?</a>
                    </div>

                    <button type="submit" class="btn-signin">Sign In</button>
                </form>

                <div class="social-row">
                    <a href="#" class="social-btn fb" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-btn tw" title="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-btn ig" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-btn li" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>

                <div class="signup-text">
                    Don't have an account? <a href="registration.php">Register here</a>
                </div>
            </div>

            <div class="image-section">
                <img src="https://images.unsplash.com/photo-1529139574466-a303027c1d8b?w=600&q=80" alt="Fashion Model" />
                <div class="image-brand">
                    <h3>Elite Shoppy</h3>
                    <p>Style · Rent · Repeat</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Password toggle
        const togglePw = document.getElementById('togglePw');
        const pwInput = document.getElementById('password');
        togglePw.addEventListener('click', () => {
            const isText = pwInput.type === 'text';
            pwInput.type = isText ? 'password' : 'text';
            togglePw.querySelector('i').className = isText ? 'fas fa-eye' : 'fas fa-eye-slash';
        });

        // Real AJAX login
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const alertBox = document.getElementById('alertBox');
            const btn = document.querySelector('.btn-signin');
            
            alertBox.style.display = 'none';
            btn.disabled = true;
            btn.textContent = 'Signing In...';

            const formData = new FormData();
            formData.append('email', email);
            formData.append('password', password);

            fetch('process_login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alertBox.textContent = data.message;
                    alertBox.className = 'alert success';
                    alertBox.style.display = 'block';
                    setTimeout(() => { window.location.href = 'Home.php'; }, 1200);
                } else {
                    alertBox.textContent = data.message;
                    alertBox.className = 'alert error';
                    alertBox.style.display = 'block';
                }
                btn.disabled = false;
                btn.textContent = 'Sign In';
            })
            .catch(error => {
                alertBox.textContent = 'Login error. Please try again.';
                alertBox.className = 'alert error';
                alertBox.style.display = 'block';
                btn.disabled = false;
                btn.textContent = 'Sign In';
            });
        });
    </script>
</body>
</html>
