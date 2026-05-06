<script>
    function addToCart(id) {
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `product_id=${id}`
    })
    .then(res => res.text())
    .then(data => {
        if (data === "login_required") {
            window.location.href = "login.php";
        } else {
            alert("Added to cart");
            // Update cart count across all pages
            window.dispatchEvent(new StorageEvent('storage', {
                key: 'eliteshoppy_cart',
                newValue: localStorage.getItem('eliteshoppy_cart')
            }));
        }
    });
}
    </script>
<?php
// Database connection - include from admin folder
session_start();
include('../admin/connect.php');

// ── USER PROFILE DATA ──
$user = null;
$wishlist_count = 0;
if (isset($_SESSION['user_id']) && $con) {
    $user_query = "SELECT fname, lname, email, mobno, gender FROM user_master WHERE uid = ? AND status = 1";
    $stmt = mysqli_prepare($con, $user_query);
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    if ($user) {
        // Wishlist count (error-safe)
        $wishlist_count = 0;
        // Skip wishlist query if table doesn't exist
        $table_check = mysqli_query($con, "SHOW TABLES LIKE 'wishlist'");
        if (mysqli_num_rows($table_check) > 0) {
            $wishlist_q = "SELECT COUNT(*) as cnt FROM wishlist WHERE uid = ?";
            $stmt = mysqli_prepare($con, $wishlist_q);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
                mysqli_stmt_execute($stmt);
                $wishlist_result = mysqli_stmt_get_result($stmt);
                $wishlist_data = mysqli_fetch_assoc($wishlist_result);
                $wishlist_count = $wishlist_data ? (int)$wishlist_data['cnt'] : 0;
                mysqli_stmt_close($stmt);
            }
        }
    }
}

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

// Fetch products from database
$products = [];
if ($con) {
    $query = "SELECT * FROM product_master WHERE status = 1 ORDER BY pid DESC LIMIT 12";
    $result = mysqli_query($con, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
    }
    
    // Fetch categories from database
    $categories = [];
    $cat_query = "SELECT * FROM category_master WHERE status = 1 ORDER BY cid DESC";
    $cat_result = mysqli_query($con, $cat_query);
    if ($cat_result) {
        while ($row = mysqli_fetch_assoc($cat_result)) {
            $categories[] = $row;
        }
    }
    
    // Fetch brands from database
    $brands = [];
    $brand_query = "SELECT * FROM brand_master WHERE status = 1 ORDER BY bid DESC";
    $brand_result = mysqli_query($con, $brand_query);
    if ($brand_result) {
        while ($row = mysqli_fetch_assoc($brand_result)) {
            $brands[] = $row;
        }
    }
}

// Category fallback images
$catFallbackImages = [
    'https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?w=600&q=80',
    'https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=600&q=80',
    'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600&q=80',
    'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80',
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elite Shoppy - Style Rent Repeat</title>
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
            flex-wrap: wrap;
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

        .top-bar .top-links a:hover { 
            color: var(--teal); 
            transform: translateY(-2px);
        }
        .top-bar .top-links a i { 
            color: var(--teal); 
            font-size: 12px;
        }

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
            font-weight: 500;
        }

        .top-bar .top-right a:hover { 
            color: var(--teal); 
        }

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
            background: #fff;
        }

        .search-box:focus-within { 
            border-color: var(--teal); 
            box-shadow: 0 0 0 4px rgba(0,196,180,0.1);
        }

        .search-box input {
            flex: 1;
            border: none;
            outline: none;
            padding: 14px 24px;
            font-family: var(--font-body);
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

        .search-box button:hover { 
            background: var(--teal-dark); 
        }

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
            line-height: 1;
            border-radius: 8px;
        }

        .logo-text {
            font-family: var(--font-display);
            font-size: 32px;
            font-weight: 700;
            color: var(--black);
            letter-spacing: -1px;
        }

        .logo-tagline {
            font-size: 10px;
            color: var(--mid-gray);
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-top: 4px;
            font-family: var(--font-body);
            font-weight: 600;
        }

        .share-area {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 15px;
        }

        .share-area span {
            font-size: 13px;
            color: #888;
            font-weight: 500;
        }

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

        .social-btn:hover { 
            opacity: 0.85; 
            transform: translateY(-3px); 
            color: white; 
        }
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
            letter-spacing: 0.3px;
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

        .nav-dropdown li a i { 
            color: var(--teal); 
            width: 18px;
            font-size: 12px;
        }

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

        .nav-cart:hover { 
            background: var(--teal-dark); 
            color: white; 
        }

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

        /* ── HERO SLIDER ── */
        .hero-slider {
            position: relative;
            overflow: hidden;
            background: #222;
            height: 600px;
        }

        .slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity 0.8s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .slide.active { opacity: 1; }

        .slide-bg {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            transform: scale(1.05);
            transition: transform 6s ease;
        }

        .slide.active .slide-bg { transform: scale(1); }

        .slide-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to right, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.3) 60%, transparent 100%);
        }

        .slide-content {
            position: relative;
            z-index: 2;
            max-width: 1400px;
            width: 100%;
            padding: 0 60px;
        }

        .slide-tag {
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: var(--teal);
            margin-bottom: 16px;
            animation: slideUp 0.8s ease forwards;
        }

        .slide-title {
            font-family: var(--font-display);
            font-size: 72px;
            font-weight: 800;
            color: white;
            line-height: 1.1;
            margin-bottom: 16px;
            animation: slideUp 0.8s ease 0.2s forwards;
            opacity: 0;
        }

        .slide-title .highlight { color: var(--teal); }

        .slide-sub {
            font-size: 20px;
            color: rgba(255,255,255,0.8);
            margin-bottom: 40px;
            letter-spacing: 3px;
            font-style: italic;
            animation: slideUp 0.8s ease 0.4s forwards;
            opacity: 0;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .btn-shop-now {
            display: inline-block;
            background: var(--teal);
            color: white;
            padding: 16px 50px;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            border-radius: 4px;
            animation: slideUp 0.8s ease 0.6s forwards;
            opacity: 0;
        }

        .btn-shop-now:hover { 
            background: var(--teal-dark); 
            transform: translateX(5px); 
            color: white; 
            box-shadow: 0 10px 30px rgba(0,196,180,0.3);
        }

        .slider-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            background: rgba(255,255,255,0.15);
            border: 2px solid rgba(255,255,255,0.3);
            color: white;
            width: 55px;
            height: 55px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 20px;
            transition: all 0.3s;
            border-radius: 50%;
        }

        .slider-arrow:hover { 
            background: var(--teal); 
            border-color: var(--teal); 
        }
        .slider-arrow.prev { left: 30px; }
        .slider-arrow.next { right: 30px; }

        .slider-dots {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 12px;
            z-index: 10;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255,255,255,0.4);
            cursor: pointer;
            transition: all 0.3s;
        }

        .dot.active { 
            background: var(--teal); 
            transform: scale(1.3); 
        }

        /* ── CATEGORY SECTION ── */
        .cat-section {
            padding: 80px 20px;
            background: var(--light-gray);
        }

        .cat-inner {
            max-width: 1400px;
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

        .section-title p { 
            color: var(--mid-gray); 
            font-size: 16px; 
        }

        .cat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
        }

        .cat-card {
            position: relative;
            height: 350px;
            overflow: hidden;
            cursor: pointer;
            border-radius: 12px;
            text-decoration: none;
        }

        .cat-card .cat-bg {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .cat-card:hover .cat-bg {
            transform: scale(1.1);
        }

        .cat-card .cat-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.2) 50%, transparent 100%);
            transition: background 0.4s;
        }

        .cat-card:hover .cat-overlay {
            background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.3) 50%, transparent 100%);
        }

        .cat-card .cat-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 4;
            padding: 30px;
            text-align: center;
        }

        .cat-card .cat-name {
            font-size: 22px;
            font-weight: 700;
            color: white;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            transform: translateY(0);
            transition: transform 0.3s;
        }

        .cat-card:hover .cat-name {
            transform: translateY(-5px);
        }

        .cat-card .cat-count {
            font-size: 14px;
            color: rgba(255,255,255,0.8);
            font-weight: 500;
        }

        .cat-card .cat-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 15px;
            background: white;
            color: var(--black);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s;
            border-radius: 25px;
        }

        .cat-card:hover .cat-btn {
            opacity: 1;
            transform: translateY(0);
        }

        .cat-card .cat-btn:hover {
            background: var(--teal);
            color: white;
        }

        /* ── PRODUCTS SECTION ── */
        .products-section {
            max-width: 1400px;
            margin: 80px auto;
            padding: 0 20px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
        }

        .product-card {
            background: white;
            border: 1px solid #eee;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s;
        }

        .product-card:hover {
            border-color: var(--teal);
            box-shadow: 0 15px 40px rgba(0,196,180,0.15);
            transform: translateY(-8px);
        }

        .product-img {
            height: 280px;
            background: var(--light-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .product-img i { 
            font-size: 80px; 
            color: #ddd; 
            transition: transform 0.4s; 
        }
        .product-card:hover .product-img i { transform: scale(1.1); }

        .product-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: var(--teal);
            color: white;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 6px 14px;
            text-transform: uppercase;
            border-radius: 20px;
        }

        .product-badge.dark { background: var(--black); }



        .product-info {
            padding: 24px;
        }

        .product-cat {
            font-size: 12px;
            color: var(--teal);
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .product-name {
            font-size: 17px;
            font-weight: 600;
            color: var(--black);
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .product-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .product-price {
            font-family: var(--font-display);
            font-size: 22px;
            font-weight: 700;
            color: var(--black);
        }

        .product-price .per-day {
            font-size: 12px;
            color: var(--mid-gray);
            font-family: var(--font-body);
            font-weight: 400;
        }

        .btn-cart {
            background: var(--black);
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 25px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-cart:hover { 
            background: var(--teal); 
        }

        /* ── BRANDS SECTION ── */
        .brands-section {
            background: var(--light-gray);
            padding: 80px 20px;
        }

        .brands-inner {
            max-width: 1400px;
            margin: 0 auto;
        }

        .brands-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
        }

        .brand-card {
            background: white;
            border-radius: 12px;
            padding: 35px 45px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            transition: all 0.3s;
            cursor: pointer;
            min-width: 180px;
        }

        .brand-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,196,180,0.15);
        }

        .brand-logo {
            width: 120px;
            height: 90px;
            margin: 0 auto 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #eee;
            border-radius: 8px;
            overflow: hidden;
            transition: border-color 0.3s;
            background: #fafafa;
        }

        .brand-card:hover .brand-logo {
            border-color: var(--teal);
        }

        .brand-logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .brand-logo i {
            font-size: 36px;
            color: #ccc;
        }

        .brand-name {
            font-size: 15px;
            font-weight: 600;
            color: var(--black);
            transition: color 0.3s;
        }

        .brand-card:hover .brand-name {
            color: var(--teal);
        }

        /* ── PROMO BANNER ── */
        .promo-strip {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            padding: 80px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .promo-strip::before {
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

        .promo-strip h2 { 
            font-family: var(--font-display); 
            font-size: 48px; 
            color: white; 
            margin-bottom: 15px;
            position: relative;
        }
        .promo-strip h2 span { color: var(--teal); }
        .promo-strip p { 
            color: rgba(255,255,255,0.6); 
            font-size: 18px; 
            margin-bottom: 35px;
            position: relative;
        }

        .btn-promo {
            display: inline-block;
            border: 2px solid var(--teal);
            color: var(--teal);
            padding: 15px 50px;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 1px;
            text-decoration: none;
            text-transform: uppercase;
            transition: all 0.3s;
            border-radius: 4px;
            position: relative;
        }

        .btn-promo:hover { 
            background: var(--teal); 
            color: white; 
            box-shadow: 0 10px 30px rgba(0,196,180,0.3);
        }

        /* ── FOOTER ── */
        footer { 
            background: #0a0a0a; 
            padding: 70px 20px 0; 
        }

        .footer-inner {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 60px;
            padding-bottom: 50px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .footer-brand .f-logo { 
            display: flex; 
            align-items: center; 
            gap: 8px; 
            margin-bottom: 20px; 
        }
        .footer-brand .f-logo .fl { 
            background: var(--teal); 
        }

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

        .footer-socials a:hover { 
            border-color: var(--teal); 
            color: var(--teal); 
            transform: translateY(-3px);
        }

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

        .footer-col ul li a:before { 
            content: '›'; 
            color: var(--teal); 
            font-size: 18px; 
            font-weight: 700;
        }
        .footer-col ul li a:hover { 
            color: var(--teal); 
            transform: translateX(5px);
        }

        .footer-bottom {
            max-width: 1400px;
            margin: 0 auto;
            padding: 25px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-bottom p { 
            color: rgba(255,255,255,0.3); 
            font-size: 14px; 
        }

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
            .cat-grid { grid-template-columns: repeat(2, 1fr); }
            .footer-inner { grid-template-columns: 1fr 1fr; }
            .slide-title { font-size: 48px; }
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
            .slide-title { font-size: 36px; }
            .hero-slider { height: 450px; }
            .slide-content { padding: 0 30px; }
            .cat-grid { grid-template-columns: 1fr; }
            .footer-inner { grid-template-columns: 1fr; gap: 40px; }
            .footer-bottom { flex-direction: column; gap: 15px; text-align: center; }
        }

@media (max-width: 480px) {
            .slide-title { font-size: 28px; }
            .section-title h2 { font-size: 32px; }
        }

        /* ── PROFILE DROPDOWN ── */
        .profile-dropdown { position: relative; }
        .profile-toggle {
            color: #ccc; text-decoration: none; display: flex; align-items: center; gap: 8px;
            transition: all 0.3s; position: relative;
        }
        .profile-toggle:hover { color: var(--teal); }
        .wishlist-badge {
            background: var(--teal); color: white; border-radius: 50%; width: 18px; height: 18px;
            font-size: 10px; font-weight: 700; display: flex; align-items: center; justify-content: center;
            position: absolute; top: -8px; right: -8px;
        }
        .profile-menu {
            position: absolute; top: 100%; right: 0; background: white; min-width: 200px;
            border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); opacity: 0; visibility: hidden;
            transform: translateY(10px); transition: all 0.3s; z-index: 1000; list-style: none; padding: 10px 0;
        }
        .profile-dropdown:hover .profile-menu {
            opacity: 1; visibility: visible; transform: translateY(0);
        }
        .profile-menu a {
            display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: #333;
            text-decoration: none; font-weight: 500; transition: all 0.2s;
        }
        .profile-menu a:hover { background: var(--teal); color: white; padding-left: 24px; }
        .profile-menu i { width: 20px; color: var(--teal); }


    </style>

</head>
<body>

    <!-- ── TOP INFO BAR ── -->
    <div class="top-bar">
        <div class="inner">
            <div class="top-links">
                <?php if ($user): ?>
                    <div class="profile-dropdown">
                        <a href="#" class="profile-toggle">
                            <i class="fas fa-user-circle"></i>
                            <span><?php echo htmlspecialchars(substr($user['fname'], 0, 12)); ?>...</span>
                            <?php if ($wishlist_count > 0): ?>
                                <span class="wishlist-badge"><?php echo $wishlist_count; ?></span>
                            <?php endif; ?>
                            <i class="fas fa-chevron-down"></i>
                        </a>
                        <div class="profile-menu">
                            <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                            <a href="profile.php#wishlist"><i class="fas fa-heart"></i> Wishlist (<?php echo $wishlist_count; ?>)</a>
                            <a href="profile.php#orders"><i class="fas fa-shopping-bag"></i> Orders</a>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php"><i class="fas fa-lock"></i> Sign In</a>
                    <a href="registration.php"><i class="fas fa-user-plus"></i> Sign Up</a>
                <?php endif; ?>
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
                <li class="active"><a href="Home.php"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="about.php"><i class="fas fa-info-circle"></i> About</a></li>

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
                            <li><a href="#"><i class="fas fa-user-tie"></i> Wedding Wear</a></li>
                            <li><a href="#"><i class="fas fa-sync-alt"></i> Rentals</a></li>
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

    <!-- ── HERO SLIDER ── -->
    <section class="hero-slider">
        <div class="slide active">
            <div class="slide-bg" style="background-image: url('Slide1.jpg');"></div>
            <div class="slide-overlay"></div>
            <div class="slide-content">
                <div class="slide-tag">New Collection 2025</div>
                <div class="slide-title">THE BIGGEST<br><span class="highlight">SALE</span></div>
                <div class="slide-sub">Special for today</div>
                <a href="#shop" class="btn-shop-now">Shop Now <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>

        <div class="slide">
            <div class="slide-bg" style="background-image: url('Slide2.jpg');"></div>
            <div class="slide-overlay"></div>
            <div class="slide-content">
                <div class="slide-tag">Exclusive Rentals</div>
                <div class="slide-title">STYLE <span class="highlight">RENT</span><br>REPEAT</div>
                <div class="slide-sub">Premium outfits for every occasion</div>
                <a href="#shop" class="btn-shop-now">Explore Rentals <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>

        <div class="slide">
            <div class="slide-bg" style="background-image: url('slide3.jpg');"></div>
            <div class="slide-overlay"></div>
            <div class="slide-content">
                <div class="slide-tag">Wedding Season</div>
                <div class="slide-title">BRIDAL <span class="highlight">LUXURY</span><br>COLLECTION</div>
                <div class="slide-sub">Crafted for your perfect moments</div>
                <a href="#shop" class="btn-shop-now">View Collection <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>

        <div class="slider-arrow prev" id="prevSlide"><i class="fas fa-chevron-left"></i></div>
        <div class="slider-arrow next" id="nextSlide"><i class="fas fa-chevron-right"></i></div>

        <div class="slider-dots">
            <div class="dot active" data-index="0"></div>
            <div class="dot" data-index="1"></div>
            <div class="dot" data-index="2"></div>
        </div>
    </section>

    <!-- ── CATEGORIES SECTION ── -->
    <section class="cat-section" id="categories">
        <div class="cat-inner">
            <div class="section-title">
                <h2>Shop by Category</h2>
                <div class="line"></div>
                <p>Find your perfect style for every occasion</p>
            </div>

            <div class="cat-grid">
                <?php
                $discountLabels = ['Up to 30% Off', 'Up to 50% Off', 'Up to 40% Off', 'Up to 55% Off'];
                
                if (!empty($categories)):
                    foreach ($categories as $i => $category):
                        $catImagePath = '../admin/images/' . htmlspecialchars($category['photo']);
                        $hasCatImage = !empty($category['photo']) && file_exists($catImagePath);
                        $bgImg = $hasCatImage ? $catImagePath : $catFallbackImages[$i % count($catFallbackImages)];
                        $discount = $discountLabels[$i % count($discountLabels)];
                ?>
                    <a href="category.php?cid=<?php echo $category['cid']; ?>" class="cat-card">
                        <div class="cat-bg" style="background-image: url('<?php echo $bgImg; ?>')"></div>
                        <div class="cat-overlay"></div>
                        <div class="cat-content">
                            <div class="cat-name"><?php echo htmlspecialchars($category['cname']); ?></div>
                            <div class="cat-count"><?php echo $discount; ?></div>
                            <button class="cat-btn">Shop Now <i class="fas fa-arrow-right"></i></button>
                        </div>
                    </a>
                <?php endforeach; ?>
                <?php else: ?>
                    <a href="#" class="cat-card">
                        <div class="cat-bg" style="background-image: url('https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600&q=80')"></div>
                        <div class="cat-overlay"></div>
                        <div class="cat-content">
                            <div class="cat-name">Men's Wear</div>
                            <div class="cat-count">Up to 30% Off</div>
                            <button class="cat-btn">Shop Now <i class="fas fa-arrow-right"></i></button>
                        </div>
                    </a>
                    <a href="#" class="cat-card">
                        <div class="cat-bg" style="background-image: url('https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=600&q=80')"></div>
                        <div class="cat-overlay"></div>
                        <div class="cat-content">
                            <div class="cat-name">Women's Wear</div>
                            <div class="cat-count">Up to 50% Off</div>
                            <button class="cat-btn">Shop Now <i class="fas fa-arrow-right"></i></button>
                        </div>
                    </a>
                    <a href="#" class="cat-card">
                        <div class="cat-bg" style="background-image: url('https://images.unsplash.com/photo-1519741497674-611481863552?w=600&q=80')"></div>
                        <div class="cat-overlay"></div>
                        <div class="cat-content">
                            <div class="cat-name">Wedding Wear</div>
                            <div class="cat-count">Up to 40% Off</div>
                            <button class="cat-btn">Shop Now <i class="fas fa-arrow-right"></i></button>
                        </div>
                    </a>
                    <a href="#" class="cat-card">
                        <div class="cat-bg" style="background-image: url('https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80')"></div>
                        <div class="cat-overlay"></div>
                        <div class="cat-content">
                            <div class="cat-name">Rental Clothes</div>
                            <div class="cat-count">Up to 55% Off</div>
                            <button class="cat-btn">Shop Now <i class="fas fa-arrow-right"></i></button>
                        </div>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ── PRODUCTS SECTION ── -->
    <section class="products-section" id="shop">
        <div class="section-title">
            <h2>Featured Products</h2>
            <div class="line"></div>
            <p>Our bestselling and most loved items</p>
        </div>
        <div class="products-grid">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <?php
                    $imagePath = '../admin/images/' . htmlspecialchars($product['photo']);
                    $hasImage = !empty($product['photo']) && file_exists($imagePath);
                    ?>
                    <div class="product-card" data-id="<?php echo $product['pid']; ?>" data-name="<?php echo htmlspecialchars($product['pname']); ?>" data-price="<?php echo $product['price']; ?>" data-category="Products">
                        <div class="product-img">
                            <?php if ($hasImage): ?>
                                <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($product['pname']); ?>" style="width:100%;height:100%;object-fit:cover;">
                            <?php else: ?>
                                <i class="fas fa-tshirt"></i>
                            <?php endif; ?>
                            <?php if ($product['qty'] > 0): ?>
                                <span class="product-badge">Available</span>
                            <?php else: ?>
                                <span class="product-badge dark">Out of Stock</span>
                            <?php endif; ?>

                        </div>
                        <div class="product-info">
                            <div class="product-cat">Products</div>
                            <div class="product-name"><?php echo htmlspecialchars($product['pname']); ?></div>
                            <div class="product-footer">
                                <div class="product-price">₹<?php echo number_format($product['price'], 0); ?></div>
                                <!-- <button class="btn-cart" <?php echo ($product['qty'] <= 0) ? 'disabled' : ''; ?>><i class="fas fa-shopping-cart"></i> Add</button> -->
                            <button class="btn-cart" onclick="addToCart(<?php echo $product['pid']; ?>)">
    <i class="fas fa-shopping-cart"></i>Add to Cart
</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <h4>No Products Available</h4>
                    <p>Please check back later for new arrivals.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ── BRANDS SECTION ── -->
<section class="brands-section" id="brands">
        <div class="brands-inner">
            <div class="section-title">
                <h2>Our Brands</h2>
                <div class="line"></div>
                <p>Discover premium fashion brands</p>
            </div>

            <div class="brands-grid">
                <?php foreach ($brands as $brand): ?>
                    <?php
                    $brandImagePath = '../admin/images/' . htmlspecialchars($brand['logo']);
                    $hasBrandImage = !empty($brand['logo']) && file_exists($brandImagePath);
                    ?>
                    <a href="brand.php?bid=<?php echo $brand['bid']; ?>" class="brand-card" style="text-decoration: none; display: block;">
                        <div class="brand-logo">
                            <?php if ($hasBrandImage): ?>
                                <img src="<?php echo $brandImagePath; ?>" alt="<?php echo htmlspecialchars($brand['bname']); ?>">
                            <?php else: ?>
                                <i class="fas fa-tag"></i>
                            <?php endif; ?>
                        </div>
                        <div class="brand-name"><?php echo htmlspecialchars($brand['bname']); ?></div>
                    </a>
                <?php endforeach; ?>
                <?php if (empty($brands)): ?>
                    <div class="brand-card" style="text-align: center; padding: 40px;">
                        <i class="fas fa-tags" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
                        <div style="font-size: 16px; color: #888;">No brands available yet</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ── PROMO BANNER ── -->
    <div class="promo-strip">
        <h2>Get <span>50% OFF</span> on Rentals</h2>
        <p>Book your outfit today and save big on premium rental wear</p>
        <a href="#shop" class="btn-promo">Rent Now</a>
    </div>

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
                    <li><a href="contact.php">Contact Us</a></li>
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
        // let cart = JSON.parse(localStorage.getItem('eliteshoppy_cart')) || [];

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

        // ── HERO SLIDER ──
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.dot');

        function goToSlide(n) {
            slides[currentSlide].classList.remove('active');
            dots[currentSlide].classList.remove('active');
            currentSlide = (n + slides.length) % slides.length;
            slides[currentSlide].classList.add('active');
            dots[currentSlide].classList.add('active');
        }

        document.getElementById('prevSlide').addEventListener('click', () => goToSlide(currentSlide - 1));
        document.getElementById('nextSlide').addEventListener('click', () => goToSlide(currentSlide + 1));
        dots.forEach(d => d.addEventListener('click', () => goToSlide(+d.dataset.index)));
        setInterval(() => goToSlide(currentSlide + 1), 5000);

        // ── MOBILE NAV ──
        document.getElementById('mobileToggle').addEventListener('click', function() {
            document.getElementById('navLinks').classList.toggle('open');
        });

        // ── SMOOTH SCROLL ──
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', function(e) {
                const target = document.querySelector(this.getAttribute('href'));
                if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth' }); }
            });
        });

        // ── PROFILE DROPDOWN ──
        document.querySelector('.profile-toggle')?.addEventListener('click', function(e) {
            e.preventDefault();
            this.nextElementSibling.classList.toggle('show');
        });

        // ── WISHLIST TOGGLE ──
        <?php if ($user): ?>

        <?php endif; ?>
    </script>

</body>
</html>
