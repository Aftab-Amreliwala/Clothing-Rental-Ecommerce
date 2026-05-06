<?php
// Database connection - include from admin folder
include('../admin/connect.php');

// Fetch categories for navigation menu
$navCategories = [];
if ($con) {
    $nav_cat_query = "SELECT * FROM category_master WHERE status = 1 ORDER BY cid ASC";
    $nav_cat_result = mysqli_query($con, $nav_cat_query);
    if ($nav_cat_result) {
        while ($row = mysqli_fetch_assoc($nav_cat_result)) {
            $navCategories[] = $row;
        }
    }
    
    // Fetch subcategories for each category
    foreach ($navCategories as &$cat) {
        $sid_query = "SELECT * FROM subcategory_master WHERE cid = " . $cat['cid'] . " AND status = 1 ORDER BY sid DESC";
        $sid_result = mysqli_query($con, $sid_query);
        $cat['subcategories'] = [];
        if ($sid_result) {
            while ($row = mysqli_fetch_assoc($sid_result)) {
                $cat['subcategories'][] = $row;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Elite Shoppy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --teal: #00c4b4;
            --teal-dark: #00a89a;
            --black: #111111;
            --white: #ffffff;
            --light-gray: #f8f9fa;
            --mid-gray: #6c757d;
            --font-display: 'Playfair Display', serif;
            --font-body: 'DM Sans', sans-serif;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: var(--font-body);
            background: var(--white);
            color: var(--black);
            overflow-x: hidden;
        }

        /* ── TOP INFO BAR ── */
        .top-bar {
            background: linear-gradient(90deg, #1a1a1a 0%, #2d2d2d 100%);
            padding: 10px 0;
            font-size: 13px;
            color: #ccc;
        }

        .top-bar .inner {
            max-width: 1400px;
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
            transition: all 0.3s;
            font-size: 13px;
            font-weight: 500;
        }

        .top-bar .top-links a:hover { color: var(--teal); }
        .top-bar .top-links a i { color: var(--teal); font-size: 12px; }

        .top-bar .top-right {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .top-bar .top-right a {
            color: #ccc;
            text-decoration: none;
            font-size: 13px;
            transition: all 0.3s;
        }

        .top-bar .top-right a:hover { color: var(--teal); }

        /* ── MIDDLE HEADER ── */
        .mid-header {
            background: var(--white);
            padding: 20px 0;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        }

        .mid-header .inner {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 30px;
        }

        .search-box {
            display: flex;
            border: 2px solid #e0e0e0;
            border-radius: 50px;
            overflow: hidden;
            transition: all 0.3s;
        }

        .search-box:focus-within { border-color: var(--teal); box-shadow: 0 0 0 4px rgba(0,196,180,0.1); }

        .search-box input {
            flex: 1;
            border: none;
            outline: none;
            padding: 14px 24px;
            font-size: 14px;
            color: #333;
            background: transparent;
        }

        .search-box button {
            background: var(--teal);
            border: none;
            padding: 14px 28px;
            color: white;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
        }

        .search-box button:hover { background: var(--teal-dark); }

        .logo-area { text-align: center; }

        .logo-area .logo-box {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .logo-letter {
            background: var(--black);
            color: var(--white);
            font-family: var(--font-display);
            font-size: 42px;
            font-weight: 800;
            width: 60px;
            height: 65px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }

        .logo-text {
            font-family: var(--font-display);
            font-size: 32px;
            font-weight: 700;
            color: var(--black);
        }

        .logo-tagline {
            font-size: 10px;
            color: var(--mid-gray);
            letter-spacing: 4px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .share-area {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 15px;
        }

        .share-area span { font-size: 13px; color: #888; font-weight: 500; }

        .social-btn {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.3s;
        }

        .social-btn:hover { transform: translateY(-3px); color: white; }
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
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }

        .main-nav .inner {
            max-width: 1400px;
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
            gap: 8px;
            padding: 18px 24px;
            color: var(--white);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            white-space: nowrap;
            border-bottom: 3px solid transparent;
        }

        .nav-links > li > a:hover,
        .nav-links > li > .nav-dd-toggle:hover,
        .nav-links > li.active > a { 
            color: var(--teal); 
            background: rgba(255,255,255,0.05); 
            border-bottom-color: var(--teal); 
        }

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
            min-width: 240px;
            background: white;
            border-top: 3px solid var(--teal);
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s ease;
            z-index: 999;
            list-style: none;
            padding: 10px 0;
            border-radius: 0 0 8px 8px;
        }

        .nav-links > li:hover .nav-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .nav-dropdown li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            color: #333;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .nav-dropdown li a i { color: var(--teal); width: 18px; font-size: 12px; }

        .nav-dropdown li a:hover {
            background: var(--teal);
            color: white;
            padding-left: 30px;
        }

        .nav-dropdown li a:hover i { color: white; }

        .nav-cart {
            background: var(--teal);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 35px;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            border: none;
            color: white;
            font-size: 20px;
            text-decoration: none;
        }

        .nav-cart:hover { background: var(--teal-dark); color: white; }

        .nav-cart .cart-count {
            position: absolute;
            top: 8px;
            right: 22px;
            background: white;
            color: var(--teal);
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ── PAGE HEADER ── */
        .page-header {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            padding: 80px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            inset: 0;
            background: repeating-linear-gradient(
                45deg,
                rgba(0,196,180,0.03) 0px,
                rgba(0,196,180,0.03) 2px,
                transparent 2px,
                transparent 20px
            );
        }

        .page-header h1 {
            font-family: var(--font-display);
            font-size: 56px;
            font-weight: 700;
            color: white;
            margin-bottom: 15px;
            position: relative;
        }

        .breadcrumb {
            display: flex;
            justify-content: center;
            gap: 12px;
            font-size: 15px;
            position: relative;
        }

        .breadcrumb a {
            color: var(--teal);
            text-decoration: none;
            transition: all 0.3s;
            font-weight: 500;
        }

        .breadcrumb a:hover { text-decoration: underline; color: white; }

        .breadcrumb span { color: rgba(255,255,255,0.6); }

        /* ── ABOUT CONTENT ── */
        .about-section {
            padding: 80px 20px;
            background: var(--light-gray);
        }

        .about-inner {
            max-width: 1200px;
            margin: 0 auto;
        }

        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .about-image {
            position: relative;
        }

        .about-image img {
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
        }

        .about-image::before {
            content: '';
            position: absolute;
            top: -20px;
            left: -20px;
            right: 20px;
            bottom: 20px;
            border: 3px solid var(--teal);
            border-radius: 12px;
            z-index: -1;
        }

        .about-content h2 {
            font-family: var(--font-display);
            font-size: 42px;
            font-weight: 700;
            color: var(--black);
            margin-bottom: 20px;
        }

        .about-content .line {
            width: 70px;
            height: 4px;
            background: var(--teal);
            margin-bottom: 25px;
            border-radius: 2px;
        }

        .about-content p {
            font-size: 16px;
            line-height: 1.8;
            color: var(--mid-gray);
            margin-bottom: 20px;
        }

        .about-features {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-top: 30px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            background: var(--teal);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }

        .feature-text h4 {
            font-size: 16px;
            font-weight: 600;
            color: var(--black);
            margin-bottom: 4px;
        }

        .feature-text p {
            font-size: 13px;
            color: var(--mid-gray);
            margin: 0;
        }

        /* ── STATS SECTION ── */
        .stats-section {
            background: linear-gradient(135deg, var(--teal) 0%, var(--teal-dark) 100%);
            padding: 60px 20px;
        }

        .stats-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 40px;
            text-align: center;
        }

        .stat-item h3 {
            font-family: var(--font-display);
            font-size: 48px;
            font-weight: 700;
            color: white;
            margin-bottom: 8px;
        }

        .stat-item p {
            font-size: 16px;
            color: rgba(255,255,255,0.9);
            font-weight: 500;
        }

        /* ── TEAM SECTION ── */
        .team-section {
            padding: 80px 20px;
            background: var(--white);
        }

        .team-inner {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-family: var(--font-display);
            font-size: 42px;
            font-weight: 700;
            color: var(--black);
            margin-bottom: 12px;
        }

        .section-title .line {
            width: 70px;
            height: 4px;
            background: var(--teal);
            margin: 0 auto 15px;
            border-radius: 2px;
        }

        .section-title p { color: var(--mid-gray); font-size: 16px; }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
        }

        .team-card {
            background: var(--light-gray);
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s;
        }

        .team-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,196,180,0.15);
        }

        .team-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 0 auto 20px;
            background: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: #999;
            overflow: hidden;
        }

        .team-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .team-card h4 {
            font-size: 18px;
            font-weight: 600;
            color: var(--black);
            margin-bottom: 8px;
        }

        .team-card p {
            font-size: 14px;
            color: var(--teal);
            font-weight: 500;
        }

        /* ── FOOTER ── */
        footer { background: #0a0a0a; padding: 70px 20px 0; }

        .footer-inner {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 60px;
            padding-bottom: 50px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .footer-brand .f-logo { display: flex; align-items: center; gap: 8px; margin-bottom: 20px; }

        .footer-brand p {
            color: rgba(255,255,255,0.55);
            font-size: 14px;
            line-height: 1.8;
            margin-bottom: 25px;
        }

        .footer-socials { display: flex; gap: 12px; }

        .footer-socials a {
            width: 40px;
            height: 40px;
            border: 1px solid rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255,255,255,0.5);
            font-size: 14px;
            text-decoration: none;
            transition: all 0.3s;
            border-radius: 50%;
        }

        .footer-socials a:hover { border-color: var(--teal); color: var(--teal);(-3px); transform: translateY }

        .footer-col h4 {
            color: white;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 25px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--teal);
            display: inline-block;
        }

        .footer-col ul { list-style: none; padding: 0; }
        .footer-col ul li { margin-bottom: 14px; }

        .footer-col ul li a {
            color: rgba(255,255,255,0.55);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .footer-col ul li a:before { content: '›'; color: var(--teal); font-size: 18px; font-weight: 700; }
        .footer-col ul li a:hover { color: var(--teal); transform: translateX(5px); }

        .footer-bottom {
            max-width: 1400px;
            margin: 0 auto;
            padding: 25px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-bottom p { color: rgba(255,255,255,0.3); font-size: 14px; }

        /* ── MOBILE ── */
        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            padding: 18px 20px;
            cursor: pointer;
        }

        @media (max-width: 1024px) {
            .about-grid { grid-template-columns: 1fr; gap: 40px; }
            .stats-inner { grid-template-columns: repeat(2, 1fr); }
            .team-grid { grid-template-columns: repeat(2, 1fr); }
            .footer-inner { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 768px) {
            .top-bar .top-links { display: none; }
            .mid-header .inner { grid-template-columns: 1fr; gap: 15px; }
            .share-area { justify-content: center; }
            .logo-area { order: -1; }
            .mobile-toggle { display: block; }
            .nav-links { display: none; flex-direction: column; position: absolute; top: 100%; left: 0; right: 0; background: var(--black); }
            .nav-links.open { display: flex; }
            .main-nav .inner { flex-wrap: wrap; position: relative; }
            .nav-cart { display: none; }
            .page-header h1 { font-size: 36px; }
            .about-content h2 { font-size: 32px; }
            .about-features { grid-template-columns: 1fr; }
            .stats-inner { grid-template-columns: 1fr 1fr; }
            .team-grid { grid-template-columns: 1fr; }
            .footer-inner { grid-template-columns: 1fr; gap: 40px; }
            .footer-bottom { flex-direction: column; gap: 15px; text-align: center; }
        }
    </style>
</head>
<body>

    <!-- ── TOP INFO BAR ── -->
    <div class="top-bar">
        <div class="inner">
            <div class="top-links">
                <a href="login.php"><i class="fas fa-lock"></i> Sign In</a>
                <a href="registration.php"><i class="fas fa-user-plus"></i> Sign Up</a>
                <a href="tel:01234567898"><i class="fas fa-phone"></i> Call : 01234567898</a>
                <a href="mailto:info@example.com"><i class="fas fa-envelope"></i> info@example.com</a>
            </div>
            <div class="top-right">
                <a href="#"><i class="fas fa-shipping-fast"></i> Free Shipping Over ₹999</a>
            </div>
        </div>
    </div>

    <!-- ── MIDDLE HEADER ── -->
    <div class="mid-header">
        <div class="inner">
            <div class="search-box">
                <input type="text" placeholder="Search for products, brands, categories...">
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
                <li><a href="Home.php"><i class="fas fa-home"></i> Home</a></li>
                <li class="active"><a href="about.php"><i class="fas fa-info-circle"></i> About</a></li>

                <?php if (!empty($navCategories)): ?>
                    <?php foreach ($navCategories as $navCat): ?>
                        <li>
                            <div class="nav-dd-toggle">
                                <?php echo htmlspecialchars($navCat['cname']); ?> 
                                <i class="fas fa-chevron-down chevron"></i>
                            </div>
                            <ul class="nav-dropdown">
                                <?php if (!empty($navCat['subcategories'])): ?>
                                    <?php foreach ($navCat['subcategories'] as $sub): ?>
                                        <li><a href="subcategory.php?cid=<?php echo $navCat['cid']; ?>&sid=<?php echo $sub['sid']; ?>"><i class="fas fa-angle-right"></i> <?php echo htmlspecialchars($sub['sname']); ?></a></li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li><a href="category.php?cid=<?php echo $navCat['cid']; ?>"><i class="fas fa-angle-right"></i> View All</a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>
                        <div class="nav-dd-toggle">Men's Wear <i class="fas fa-chevron-down chevron"></i></div>
                        <ul class="nav-dropdown">
                            <li><a href="#"><i class="fas fa-tshirt"></i> Top's Wear</a></li>
                            <li><a href="#"><i class="fas fa-socks"></i> Bottom's Wear</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
            </ul>

            <a href="cart.php" class="nav-cart">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count" id="cartCount">0</span>
            </a>
        </div>
    </nav>

    <!-- ── PAGE HEADER ── -->
    <div class="page-header">
        <h1>About Us</h1>
        <div class="breadcrumb">
            <a href="Home.php">Home</a>
            <span>/</span>
            <span>About Us</span>
        </div>
    </div>

    <!-- ── ABOUT SECTION ── -->
    <section class="about-section">
        <div class="about-inner">
            <div class="about-grid">
                <div class="about-image">
                    <img src="https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?w=600&q=80" alt="About Elite Shoppy">
                </div>
                <div class="about-content">
                    <h2>Welcome to Elite Shoppy</h2>
                    <div class="line"></div>
                    <p>Since 1950, Elite Shoppy has been a pioneer in the fashion industry, delivering premium quality dresses and outfits for every occasion. Our journey began with a simple vision - to make luxury fashion accessible to everyone.</p>
                    <p>Today, we continue to uphold our legacy by offering both purchase and rental options, allowing you to experience the finest craftsmanship without compromise. Whether it's your wedding day, a special celebration, or everyday elegance, we've got you covered.</p>
                    
                    <div class="about-features">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-shipping-fast"></i>
                            </div>
                            <div class="feature-text">
                                <h4>Free Shipping</h4>
                                <p>On orders over ₹999</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-undo"></i>
                            </div>
                            <div class="feature-text">
                                <h4>Easy Returns</h4>
                                <p>30-day return policy</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-headset"></i>
                            </div>
                            <div class="feature-text">
                                <h4>24/7 Support</h4>
                                <p>Dedicated customer care</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="feature-text">
                                <h4>Secure Payment</h4>
                                <p>100% secure checkout</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── STATS SECTION ── -->
    <section class="stats-section">
        <div class="stats-inner">
            <div class="stat-item">
                <h3>75+</h3>
                <p>Years of Excellence</p>
            </div>
            <div class="stat-item">
                <h3>50K+</h3>
                <p>Happy Customers</p>
            </div>
            <div class="stat-item">
                <h3>100+</h3>
                <p>Premium Brands</p>
            </div>
            <div class="stat-item">
                <h3>500+</h3>
                <p>Unique Designs</p>
            </div>
        </div>
    </section>

    <!-- ── TEAM SECTION ── -->
    <section class="team-section">
        <div class="team-inner">
            <div class="section-title">
                <h2>Our Team</h2>
                <div class="line"></div>
                <p>Meet the people behind our success</p>
            </div>

            <div class="team-grid">
                <div class="team-card">
                    <div class="team-image">
                        <i class="fas fa-user"></i>
                    </div>
                    <h4>John Smith</h4>
                    <p>Founder & CEO</p>
                </div>
                <div class="team-card">
                    <div class="team-image">
                        <i class="fas fa-user"></i>
                    </div>
                    <h4>Sarah Johnson</h4>
                    <p>Creative Director</p>
                </div>
                <div class="team-card">
                    <div class="team-image">
                        <i class="fas fa-user"></i>
                    </div>
                    <h4>Michael Chen</h4>
                    <p>Head of Design</p>
                </div>
                <div class="team-card">
                    <div class="team-image">
                        <i class="fas fa-user"></i>
                    </div>
                    <h4>Emily Davis</h4>
                    <p>Customer Relations</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ── FOOTER ── -->
    <footer id="contact">
        <div class="footer-inner">
            <div class="footer-brand">
                <div class="f-logo">
                    <div class="logo-letter fl" style="background:var(--teal);font-family:var(--font-display);font-size:28px;font-weight:800;width:42px;height:48px;display:flex;align-items:center;justify-content:center;color:white;border-radius:6px;">E</div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ── CART ──
        let cart = JSON.parse(localStorage.getItem('eliteshoppy_cart')) || [];

        function updateCartCount() {
            const total = cart.reduce((s, i) => s + i.quantity, 0);
            document.getElementById('cartCount').textContent = total;
        }

        updateCartCount();

        window.addEventListener('storage', function(e) {
            if (e.key === 'eliteshoppy_cart') {
                cart = JSON.parse(e.newValue || '[]');
                updateCartCount();
            }
        });

        // ── MOBILE NAV ──
        document.getElementById('mobileToggle').addEventListener('click', function() {
            document.getElementById('navLinks').classList.toggle('open');
        });
    </script>
</body>
</html>
