<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Please Login First</title>
    <style>body{font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0;}
    .login-msg{max-width: 500px; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); padding: 40px; border-radius: 20px; text-align: center; box-shadow: 0 20px 40px rgba(0,0,0,0.2);}
    .login-msg h2{font-size: 28px; margin-bottom: 10px; font-weight: 700;}
    .login-msg p{font-size: 16px; margin-bottom: 30px; opacity: 0.9;}
    .btn-login, .btn-register{display: inline-block; padding: 14px 30px; margin: 0 10px; text-decoration: none; border-radius: 50px; font-weight: 600; font-size: 14px; transition: all 0.3s; border: none; cursor: pointer;}
    .btn-login{background: #00c4b4; color: white;} .btn-login:hover{background: #00a89a;}
    .btn-register{background: #111111; color: white;} .btn-register:hover{background: #333333;}</style>
    </head><body>
    <div class="login-msg">
        <h2>⛔ First Register/Login </h2>
        <p>Only after that you can go to the cart and place an order. Your cart is safe, it will just be visible after logging in.</p>
        <a href="login.php" class="btn-login">🔑 Login</a>
        <a href="registration.php" class="btn-register">➕ Register</a>
    </div></body></html>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Elite Shoppy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --teal: #00c4b4;
            --teal-dark: #00a89a;
            --black: #111111;
            --white: #ffffff;
            --light-gray: #f5f5f5;
            --mid-gray: #888;
            --font-display: 'Playfair Display', serif;
            --font-body: 'DM Sans', sans-serif;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: var(--font-body);
            background: var(--white);
            color: var(--black);
        }

        /* ── TOP INFO BAR ── */
        .top-bar {
            background: var(--black);
            padding: 8px 0;
            font-size: 13px;
            color: #ccc;
        }

        .top-bar .inner {
            max-width: 1300px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .top-bar .top-links {
            display: flex;
            gap: 25px;
        }

        .top-bar .top-links a {
            color: #ccc;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s;
            font-size: 13px;
        }

        .top-bar .top-links a:hover { color: var(--teal); }
        .top-bar .top-links a i { color: var(--teal); font-size: 13px; }

        .top-bar .top-right {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .top-bar .top-right a {
            color: #ccc;
            text-decoration: none;
            font-size: 13px;
            transition: color 0.2s;
        }

        .top-bar .top-right a:hover { color: var(--teal); }

        /* ── MIDDLE HEADER ── */
        .mid-header {
            background: var(--white);
            padding: 18px 0;
            border-bottom: 1px solid #eee;
        }

        .mid-header .inner {
            max-width: 1300px;
            margin: 0 auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 20px;
        }

        .search-box {
            display: flex;
            border: 2px solid #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
            transition: border-color 0.3s;
        }

        .search-box:focus-within { border-color: var(--teal); }

        .search-box input {
            flex: 1;
            border: none;
            outline: none;
            padding: 10px 18px;
            font-family: var(--font-body);
            font-size: 14px;
            color: #333;
            background: transparent;
        }

        .search-box button {
            background: var(--teal);
            border: none;
            padding: 10px 20px;
            color: white;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.2s;
        }

        .search-box button:hover { background: var(--teal-dark); }

        .logo-area { text-align: center; }

        .logo-area .logo-box {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
        }

        .logo-letter {
            background: var(--black);
            color: var(--white);
            font-family: var(--font-display);
            font-size: 38px;
            font-weight: 800;
            width: 50px;
            height: 55px;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }

        .logo-text {
            font-family: var(--font-display);
            font-size: 34px;
            font-weight: 700;
            color: var(--black);
            letter-spacing: -1px;
        }

        .logo-tagline {
            font-size: 10px;
            color: var(--mid-gray);
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-top: 4px;
            font-family: var(--font-body);
        }

        .share-area {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 12px;
        }

        .share-area span {
            font-size: 13px;
            color: #888;
            font-weight: 500;
        }

        .social-btn {
            width: 34px;
            height: 34px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            text-decoration: none;
            transition: opacity 0.2s, transform 0.2s;
        }

        .social-btn:hover { opacity: 0.85; transform: translateY(-2px); color: white; }
        .social-btn.fb { background: #3b5998; }
        .social-btn.tw { background: #1da1f2; }
        .social-btn.ig { background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); }
        .social-btn.li { background: #0077b5; }

        /* ── NAVBAR ── */
        .main-nav {
            background: var(--black);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .main-nav .inner {
            max-width: 1300px;
            margin: 0 auto;
            display: flex;
            align-items: stretch;
        }

        .nav-links {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            flex: 1;
        }

        .nav-links > li { position: relative; }

        .nav-links > li > a,
        .nav-links > li > .nav-dd-toggle {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 18px 22px;
            color: var(--white);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 0.3px;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
            white-space: nowrap;
            border-bottom: 3px solid transparent;
            user-select: none;
        }

        .nav-links > li > a:hover,
        .nav-links > li > .nav-dd-toggle:hover,
        .nav-links > li.active > a { color: var(--teal); border-bottom-color: var(--teal); }

        .nav-links > li > a i.chevron,
        .nav-links > li > .nav-dd-toggle i.chevron {
            font-size: 10px;
            transition: transform 0.3s;
        }

        .nav-links > li:hover > .nav-dd-toggle i.chevron { transform: rotate(180deg); }

        .nav-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            min-width: 210px;
            background: white;
            border-top: 3px solid var(--teal);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.25s ease;
            z-index: 999;
            list-style: none;
            padding: 8px 0;
        }

        .nav-links > li:hover .nav-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .nav-dropdown li a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 22px;
            color: #333;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .nav-dropdown li a i { color: var(--teal); width: 16px; }

        .nav-dropdown li a:hover {
            background: var(--teal);
            color: white;
            padding-left: 28px;
        }

        .nav-dropdown li a:hover i { color: white; }

        .nav-cart {
            background: var(--teal);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 30px;
            cursor: pointer;
            transition: background 0.2s;
            position: relative;
            border: none;
            color: white;
            font-size: 22px;
            text-decoration: none;
        }

        .nav-cart:hover { background: var(--teal-dark); color: white; }

        .nav-cart .cart-count {
            position: absolute;
            top: 12px;
            right: 20px;
            background: white;
            color: var(--teal);
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ── CART PAGE STYLES ── */
        .cart-page {
            max-width: 1300px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .cart-page-title {
            font-family: var(--font-display);
            font-size: 42px;
            font-weight: 700;
            color: var(--black);
            text-align: center;
            margin-bottom: 10px;
        }

        .cart-page-subtitle {
            text-align: center;
            color: var(--mid-gray);
            font-size: 16px;
            margin-bottom: 40px;
        }

        .cart-breadcrumb {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .cart-breadcrumb a {
            color: var(--mid-gray);
            text-decoration: none;
            transition: color 0.2s;
        }

        .cart-breadcrumb a:hover { color: var(--teal); }

        .cart-breadcrumb span {
            color: var(--black);
            font-weight: 600;
        }

        .cart-breadcrumb i {
            color: var(--mid-gray);
            font-size: 10px;
        }

        /* Cart Content Grid */
        .cart-content-grid {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 30px;
            align-items: start;
        }

        /* Cart Items Section */
        .cart-items-section {
            background: white;
            border: 1px solid #ececec;
            border-radius: 4px;
            overflow: hidden;
        }

        .cart-header {
            display: grid;
            grid-template-columns: 1fr 120px 120px 100px;
            gap: 15px;
            padding: 18px 25px;
            background: var(--black);
            color: white;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .cart-item-row {
            display: grid;
            grid-template-columns: 1fr 120px 120px 100px;
            gap: 15px;
            padding: 25px;
            border-bottom: 1px solid #f0f0f0;
            align-items: center;
            transition: background 0.2s;
        }

        .cart-item-row:hover {
            background: var(--light-gray);
        }

        .cart-item-row:last-child {
            border-bottom: none;
        }

        .cart-item-product {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .cart-item-image {
            width: 80px;
            height: 80px;
            background: var(--light-gray);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
        }

        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cart-item-image i {
            font-size: 28px;
            color: #ccc;
        }

        .cart-item-details h4 {
            font-size: 15px;
            font-weight: 600;
            color: var(--black);
            margin-bottom: 6px;
        }

        .cart-item-details p {
            font-size: 13px;
            color: var(--mid-gray);
        }

        .cart-item-price {
            font-family: var(--font-display);
            font-size: 18px;
            font-weight: 700;
            color: var(--black);
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--light-gray);
            padding: 5px 10px;
            border-radius: 4px;
            width: fit-content;
        }

        .quantity-btn {
            width: 28px;
            height: 28px;
            border: none;
            background: var(--black);
            color: white;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }

        .quantity-btn:hover {
            background: var(--teal);
        }

        .quantity-display {
            min-width: 35px;
            text-align: center;
            font-weight: 600;
            color: var(--black);
        }

        .cart-item-total {
            font-family: var(--font-display);
            font-size: 18px;
            font-weight: 700;
            color: var(--teal);
        }

        .remove-item-btn {
            background: none;
            border: none;
            color: #999;
            cursor: pointer;
            font-size: 18px;
            transition: color 0.2s;
            padding: 5px;
        }

        .remove-item-btn:hover {
            color: #e74c3c;
        }

        /* Empty Cart */
        .empty-cart {
            text-align: center;
            padding: 80px 40px;
        }

        .empty-cart i {
            font-size: 80px;
            color: #ddd;
            margin-bottom: 25px;
        }

        .empty-cart h3 {
            font-family: var(--font-display);
            font-size: 28px;
            font-weight: 700;
            color: var(--black);
            margin-bottom: 12px;
        }

        .empty-cart p {
            color: var(--mid-gray);
            font-size: 15px;
            margin-bottom: 30px;
        }

        .btn-continue-shopping {
            display: inline-block;
            background: var(--teal);
            color: white;
            padding: 14px 40px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: background 0.2s;
        }

        .btn-continue-shopping:hover {
            background: var(--teal-dark);
            color: white;
        }

        /* Cart Summary */
        .cart-summary {
            background: white;
            border: 1px solid #ececec;
            border-radius: 4px;
            padding: 30px;
            position: sticky;
            top: 100px;
        }

        .summary-title {
            font-family: var(--font-display);
            font-size: 22px;
            font-weight: 700;
            color: var(--black);
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--teal);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 14px;
            color: var(--mid-gray);
        }

        .summary-row span:last-child {
            color: var(--black);
            font-weight: 600;
        }

        .summary-row.total {
            font-size: 18px;
            font-weight: 700;
            color: var(--black);
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
        }

        .summary-row.total span:last-child {
            color: var(--teal);
            font-size: 22px;
        }

        .btn-checkout {
            width: 100%;
            padding: 16px;
            background: var(--black);
            color: white;
            border: none;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            margin-top: 25px;
            transition: background 0.2s;
        }

        .btn-checkout:hover {
            background: var(--teal);
        }

        .btn-continue {
            width: 100%;
            padding: 16px;
            background: transparent;
            color: var(--black);
            border: 2px solid var(--black);
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            margin-top: 12px;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-continue:hover {
            background: var(--black);
            color: white;
        }

        .secure-checkout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
            font-size: 12px;
            color: var(--mid-gray);
        }

        .secure-checkout i {
            color: var(--teal);
        }

        /* ── FOOTER ── */
        footer { background: #111; padding: 60px 20px 0; }

        .footer-inner {
            max-width: 1300px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 50px;
            padding-bottom: 50px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .footer-brand .f-logo { display: flex; align-items: center; gap: 6px; margin-bottom: 18px; }
        .footer-brand .f-logo .fl { background: var(--teal); }

        .footer-brand p {
            color: rgba(255,255,255,0.55);
            font-size: 14px;
            line-height: 1.8;
            margin-bottom: 22px;
        }

        .footer-socials { display: flex; gap: 10px; }

        .footer-socials a {
            width: 36px;
            height: 36px;
            border: 1px solid rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255,255,255,0.5);
            font-size: 14px;
            text-decoration: none;
            transition: all 0.2s;
            border-radius: 3px;
        }

        .footer-socials a:hover { border-color: var(--teal); color: var(--teal); }

        .footer-col h4 {
            color: white;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 22px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--teal);
            display: inline-block;
        }

        .footer-col ul { list-style: none; padding: 0; }
        .footer-col ul li { margin-bottom: 11px; }

        .footer-col ul li a {
            color: rgba(255,255,255,0.5);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footer-col ul li a:before { content: '›'; color: var(--teal); font-size: 16px; }
        .footer-col ul li a:hover { color: var(--teal); }

        .footer-bottom {
            max-width: 1300px;
            margin: 0 auto;
            padding: 22px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-bottom p { color: rgba(255,255,255,0.3); font-size: 13px; }

        /* ── MOBILE ── */
        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 22px;
            padding: 18px 20px;
            cursor: pointer;
        }

        @media (max-width: 1024px) {
            .cart-content-grid {
                grid-template-columns: 1fr;
            }
            .cart-summary {
                position: relative;
                top: 0;
            }
            .footer-inner { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 768px) {
            .top-bar .top-links { display: none; }
            .mid-header .inner { grid-template-columns: 1fr; gap: 14px; }
            .share-area { justify-content: center; }
            .logo-area { order: -1; }
            .mobile-toggle { display: block; }
            .nav-links { display: none; flex-direction: column; }
            .nav-links.open { display: flex; }
            .main-nav .inner { flex-wrap: wrap; }
            
            .cart-header {
                display: none;
            }
            
            .cart-item-row {
                grid-template-columns: 1fr;
                gap: 15px;
                text-align: center;
            }
            
            .cart-item-product {
                flex-direction: column;
            }
            
            .quantity-control {
                margin: 0 auto;
            }
            
            .remove-item-btn {
                position: absolute;
                top: 10px;
                right: 10px;
            }
            
            .cart-item-row {
                position: relative;
            }
            
            .footer-inner { grid-template-columns: 1fr; gap: 30px; }
            .footer-bottom { flex-direction: column; gap: 10px; text-align: center; }
        }
    </style>
</head>
<body>

    <!-- ── TOP INFO BAR ── -->
    <div class="top-bar">
        <div class="inner">
            <div class="top-links">
                <a href="login.php"><i class="fas fa-lock"></i> Sign In</a>
                <a href="registration.php"><i class="fas fa-pen"></i> Sign Up</a>
                <a href="tel:01234567898"><i class="fas fa-phone"></i> Call : 01234567898</a>
                <a href="mailto:info@example.com"><i class="fas fa-envelope"></i> info@example.com</a>
            </div>
            <div class="top-right">
                <a href="#"><i class="fas fa-truck"></i> Free Shipping Over ₹999</a>
            </div>
        </div>
    </div>

    <!-- ── MIDDLE HEADER ── -->
    <div class="mid-header">
        <div class="inner">
            <div class="search-box">
                <input type="text" placeholder="Search here...">
                <button><i class="fas fa-search"></i></button>
            </div>

            <div class="logo-area">
                <a href="Home.php" class="logo-box">
                    <div class="logo-letter">E</div>
                    <div>
                        <div class="logo-text">lite Shoppy</div>
                        <div class="logo-tagline">Style · Rent · Repeat</div>
                    </div>
                </a>
            </div>

            <div class="share-area">
                <span>Share On :</span>
                <a href="#" class="social-btn fb"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-btn tw"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-btn ig"><i class="fab fa-instagram"></i></a>
                <a href="#" class="social-btn li"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
    </div>

    <!-- ── MAIN NAVBAR ── -->
    <nav class="main-nav" id="mainNav">
        <div class="inner">
            <button class="mobile-toggle" id="mobileToggle">
                <i class="fas fa-bars"></i>
            </button>

            <ul class="nav-links" id="navLinks">
                <li><a href="Home.php">Home</a></li>
<li><a href="about.php">About</a></li>

                <li>
                    <div class="nav-dd-toggle">Men's Wear <i class="fas fa-chevron-down chevron"></i></div>
                    <ul class="nav-dropdown">
                        <li><a href="#"><i class="fas fa-tshirt"></i> Top's Wear</a></li>
                        <li><a href="#"><i class="fas fa-socks"></i> Bottom's Wear</a></li>
                        <li><a href="#"><i class="fas fa-user-tie"></i> Wedding Wear</a></li>
                        <li><a href="#"><i class="fas fa-sync-alt"></i> Rentals</a></li>
                    </ul>
                </li>

                <li>
                    <div class="nav-dd-toggle">Women's Wear <i class="fas fa-chevron-down chevron"></i></div>
                    <ul class="nav-dropdown">
                        <li><a href="#"><i class="fas fa-tshirt"></i> Top's Wear</a></li>
                        <li><a href="#"><i class="fas fa-socks"></i> Bottom's Wear</a></li>
                        <li><a href="#"><i class="fas fa-female"></i> Wedding Wear</a></li>
                        <li><a href="#"><i class="fas fa-sync-alt"></i> Rentals</a></li>
                    </ul>
                </li>

                <li>
                    <div class="nav-dd-toggle">Wedding Wear <i class="fas fa-chevron-down chevron"></i></div>
                    <ul class="nav-dropdown">
                        <li><a href="#"><i class="fas fa-ring"></i> Sherwani</a></li>
                        <li><a href="#"><i class="fas fa-crown"></i> Lehenga</a></li>
                        <li><a href="#"><i class="fas fa-gem"></i> Bridal Suits</a></li>
                        <li><a href="#"><i class="fas fa-sync-alt"></i> Rentals</a></li>
                    </ul>
                </li>

                <li>
                    <div class="nav-dd-toggle">Rental Wear <i class="fas fa-chevron-down chevron"></i></div>
                    <ul class="nav-dropdown">
                        <li><a href="#"><i class="fas fa-tshirt"></i> Casual Wear</a></li>
                        <li><a href="#"><i class="fas fa-magic"></i> Party Wear</a></li>
                        <li><a href="#"><i class="fas fa-ring"></i> Wedding Wear</a></li>
                        <li><a href="#"><i class="fas fa-info-circle"></i> How It Works</a></li>
                    </ul>
                </li>

<li><a href="contact.php">Contact</a></li>
            </ul>

            <a href="cart.php" class="nav-cart">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count" id="cartCount">0</span>
            </a>
        </div>
    </nav>

    <!-- ── CART PAGE CONTENT ── -->
    <div class="cart-page">
        <h1 class="cart-page-title">Shopping Cart</h1>
        <p class="cart-page-subtitle">Review your items before checkout</p>
        
        <div class="cart-breadcrumb">
            <a href="Home.php">Home</a>
            <i class="fas fa-chevron-right"></i>
            <span>Shopping Cart</span>
        </div>

        <!-- Cart Content -->
        <div id="cartContent"></div>
    </div>
<?php
if(!isset($_SESSION['user_id'])){
    header('location:login.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Cart - Elite Shoppy</title>
</head>

<body>

<!-- ===== YOUR EXISTING DESIGN SAME ===== -->
<div id="cartContent"></div>

<!-- ===== JS START ===== -->
</script>
<script>

let cart = [];

// LOAD CART FROM DATABASE
function loadCart() {
    fetch('fetch_cart.php')
    .then(res => res.json())
    .then(data => {
        console.log("Cart Data:", data);
        cart = data;
        renderCart();
        updateCartCount();
    });
}

// RENDER CART (YOUR DESIGN)
function renderCart() {

    let html = '';
    let total = 0;

    if(cart.length === 0){
        html = `
        <div class="empty-cart">
            <i class="fas fa-shopping-bag"></i>
            <h3>Your Cart is Empty</h3>
            <p>Add some products to your cart</p>
            <a href="Home.php" class="btn-continue-shopping">Continue Shopping</a>
        </div>`;
    } else {

        html += `
        <div class="cart-content-grid">

            <div class="cart-items-section">

                <div class="cart-header">
                    <div>Product</div>
                    <div>Price</div>
                    <div>Quantity</div>
                    <div>Total</div>
                </div>
        `;

        cart.forEach(item => {

            let subtotal = item.price * item.quantity;
            total += subtotal;

            html += `
            <div class="cart-item-row">

                <div class="cart-item-product">
                    <div class="cart-item-image">
                        ${item.photo ? `<img src="../admin/images/${item.photo}" alt="${item.product_name}">` : '<i class="fas fa-tshirt"></i>'}
</div>
                    <div class="cart-item-details">
                        <h4>${item.product_name}</h4>
                        <p>ID: ${item.product_id}</p>
                    </div>
                </div>

                <div class="cart-item-price">₹${item.price}</div>

                <div class="quantity-control">
                    <span class="quantity-display">${item.quantity}</span>
                </div>

                <div>
                    <div class="cart-item-total">₹${subtotal}</div>
                    <button class="remove-item-btn" onclick="removeItem(${item.product_id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>

            </div>
            `;
        });

        html += `</div>

        <!-- SUMMARY -->
        <div class="cart-summary">
            <h3 class="summary-title">Order Summary</h3>

            <div class="summary-row">
                <span>Subtotal</span>
                <span>₹${total}</span>
            </div>

            <div class="summary-row">
                <span>Shipping</span>
                <span>Free</span>
            </div>

            <div class="summary-row total">
                <span>Total</span>
                <span>₹${total}</span>
            </div>

            <a href='./orders/orderconfirm.php' class="btn-checkout">Proceed to Checkout</a>

            <a href="Home.php" class="btn-continue">Continue Shopping</a>

            <div class="secure-checkout">
                <i class="fas fa-lock"></i> Secure Checkout
            </div>
        </div>

        </div>`;
        fetch('set_total.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: 'total=' + total
});
    }

    document.getElementById("cartContent").innerHTML = html;
}

// REMOVE ITEM
function removeItem(product_id) {
    fetch('delete_cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'product_id=' + product_id
    })
    .then(() => loadCart());
}

// CART COUNT (TOP ICON)
function updateCartCount(){
    document.getElementById("cartCount").innerText = cart.length;
}

// LOAD PAGE
loadCart();

</script>
</body>
</html>
    <!-- ── FOOTER ── -->
    <footer>
        <div class="footer-inner">
            <div class="footer-brand">
                <div class="f-logo">
                    <div class="logo-letter fl" style="background:var(--teal);font-family:var(--font-display);font-size:28px;font-weight:800;width:38px;height:42px;display:flex;align-items:center;justify-content:center;color:white;">E</div>
                    <span style="font-family:var(--font-display);font-size:26px;font-weight:700;color:white;">lite Shoppy</span>
                </div>
                <p>Premium quality dresses since 1950. Crafted with passion and precision for your most special moments — buy or rent.</p>
                <div class="footer-socials">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-pinterest"></i></a>
                </div>
            </div>

            <div class="footer-col">
                <h4>Shop</h4>
                <ul>
                    <li><a href="#">New Arrivals</a></li>
                    <li><a href="#">Best Sellers</a></li>
                    <li><a href="#">Sale</a></li>
                    <li><a href="#">Collections</a></li>
                    <li><a href="#">Rentals</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>About</h4>
                <ul>
                    <li><a href="#">Our Story</a></li>
                    <li><a href="#">Craftsmanship</a></li>
                    <li><a href="#">Sustainability</a></li>
                    <li><a href="#">Careers</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Support</h4>
                <ul>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">Shipping Info</a></li>
                    <li><a href="#">Returns</a></li>
                    <li><a href="#">Size Guide</a></li>
                    <li><a href="#">FAQ</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2025 Elite Shoppy. All rights reserved.</p>
            <p>Designed with <span style="color:var(--teal)">♥</span> for fashion lovers</p>
        </div>
    </footer>

</body>
</html>

