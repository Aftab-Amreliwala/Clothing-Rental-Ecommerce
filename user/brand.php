<?php
// Database connection - include from admin folder
include('../admin/connect.php');

// Get brand ID from URL
$bid = isset($_GET['bid']) ? intval($_GET['bid']) : 0;

// Fetch current brand details
$brand = null;
if ($con && $bid > 0) {
    $brand_query = "SELECT * FROM brand_master WHERE bid = $bid AND status = 1";
    $brand_result = mysqli_query($con, $brand_query);
    if ($brand_result && mysqli_num_rows($brand_result) > 0) {
        $brand = mysqli_fetch_assoc($brand_result);
    }
    
    // Fetch products for this brand
    $products = [];
    $prod_query = "SELECT * FROM product_master WHERE bid = $bid AND status = 1 ORDER BY pid DESC";
    $prod_result = mysqli_query($con, $prod_query);
    if ($prod_result) {
        while ($row = mysqli_fetch_assoc($prod_result)) {
            $products[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $brand ? htmlspecialchars($brand['bname']) : 'Brand'; ?> - Elite Shoppy</title>
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

        .nav-links > li > a {
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
        }

        .nav-links > li > a:hover { color: var(--teal); border-bottom-color: var(--teal); }

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

        /* ── PAGE HEADER ── */
        .page-header {
            background: var(--light-gray);
            padding: 40px 20px;
            text-align: center;
        }

        .page-header h1 {
            font-family: var(--font-display);
            font-size: 36px;
            font-weight: 700;
            color: var(--black);
            margin-bottom: 10px;
        }

        .page-header .brand-logo-display {
            width: 120px;
            height: 80px;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #ddd;
            border-radius: 8px;
            background: white;
        }

        .page-header .brand-logo-display img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .page-header .brand-logo-display i {
            font-size: 32px;
            color: #ccc;
        }

        .breadcrumb {
            display: flex;
            justify-content: center;
            gap: 10px;
            font-size: 14px;
        }

        .breadcrumb a {
            color: var(--teal);
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .breadcrumb span {
            color: var(--mid-gray);
        }

        /* ── PRODUCTS SECTION ── */
        .products-section {
            max-width: 1300px;
            margin: 60px auto;
            padding: 0 20px;
        }

        .section-title {
            text-align: center;
            margin-bottom: 45px;
        }

        .section-title h2 {
            font-family: var(--font-display);
            font-size: 34px;
            font-weight: 700;
            color: var(--black);
            margin-bottom: 8px;
        }

        .section-title .line {
            width: 55px;
            height: 3px;
            background: var(--teal);
            margin: 0 auto 12px;
        }

        .section-title p { color: var(--mid-gray); font-size: 15px; }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(265px, 1fr));
            gap: 28px;
        }

        .product-card {
            background: white;
            border: 1px solid #ececec;
            border-radius: 4px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s;
        }

        .product-card:hover {
            border-color: var(--teal);
            box-shadow: 0 8px 30px rgba(0,196,180,0.1);
            transform: translateY(-4px);
        }

        .product-img {
            height: 260px;
            background: var(--light-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .product-img i { font-size: 70px; color: #ccc; transition: transform 0.4s; }
        .product-card:hover .product-img i { transform: scale(1.1); }

        .product-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-badge {
            position: absolute;
            top: 14px;
            left: 14px;
            background: var(--teal);
            color: white;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 4px 12px;
            text-transform: uppercase;
        }

        .product-badge.dark { background: var(--black); }



        .product-info {
            padding: 20px 22px;
            border-top: 1px solid #f0f0f0;
        }

        .product-cat {
            font-size: 11px;
            color: var(--teal);
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .product-name {
            font-size: 16px;
            font-weight: 600;
            color: var(--black);
            margin-bottom: 12px;
        }

        .product-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .product-price {
            font-family: var(--font-display);
            font-size: 20px;
            font-weight: 700;
            color: var(--black);
        }

        .btn-cart {
            background: var(--black);
            color: white;
            border: none;
            padding: 10px 18px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 3px;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-cart:hover { background: var(--teal); }

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
            .nav-cart { display: none; }
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
                <li><a href="#">About</a></li>
                <li><a href="#">Contact</a></li>
            </ul>

            <a href="cart.php" class="nav-cart">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count" id="cartCount">0</span>
            </a>
        </div>
    </nav>

    <!-- ── PAGE HEADER ── -->
    <div class="page-header">
        <div class="inner">
            <?php if ($brand): ?>
                <div class="brand-logo-display">
                    <?php
                    $brandImagePath = '../admin/images/' . htmlspecialchars($brand['logo']);
                    $hasBrandImage = !empty($brand['logo']) && file_exists($brandImagePath);
                    if ($hasBrandImage):
                    ?>
                        <img src="<?php echo $brandImagePath; ?>" alt="<?php echo htmlspecialchars($brand['bname']); ?>">
                    <?php else: ?>
                        <i class="fas fa-tag"></i>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <h1><?php echo $brand ? htmlspecialchars($brand['bname']) : 'Brand'; ?></h1>
            <div class="breadcrumb">
                <a href="Home.php">Home</a>
                <span>/</span>
                <span><?php echo $brand ? htmlspecialchars($brand['bname']) : 'Brand'; ?></span>
            </div>
        </div>
    </div>

    <!-- ── PRODUCTS ── -->
    <section class="products-section">
        <div class="section-title">
            <h2>Our Products</h2>
            <div class="line"></div>
            <p>Browse our premium collection</p>
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
                                <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($product['pname']); ?>">
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
                            <div class="product-cat"><?php echo $brand ? htmlspecialchars($brand['bname']) : 'Brand'; ?></div>
                            <div class="product-name"><?php echo htmlspecialchars($product['pname']); ?></div>
                            <div class="product-footer">
                                <div class="product-price">₹<?php echo number_format($product['price'], 0); ?></div>
                                <button class="btn-cart" <?php echo ($product['qty'] <= 0) ? 'disabled' : ''; ?>><i class="fas fa-shopping-cart"></i> Add</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <h4>No Products Found</h4>
                    <p>No products available from this brand yet.</p>
                    <a href="Home.php" class="btn btn-primary mt-3">Back to Home</a>
                </div>
            <?php endif; ?>

        </div>
    </section>

    <!-- ── FOOTER ── -->
    <footer id="contact">
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

        document.querySelectorAll('.btn-cart').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const card = this.closest('.product-card');
                const id = card.dataset.id;
                const existing = cart.find(i => i.id === id);
                if (existing) { existing.quantity++; }
                else { cart.push({ id, name: card.dataset.name, price: card.dataset.price, category: card.dataset.category, quantity: 1 }); }
                localStorage.setItem('eliteshoppy_cart', JSON.stringify(cart));
                updateCartCount();
                this.innerHTML = '<i class="fas fa-check"></i> Added!';
                this.style.background = 'var(--teal)';
                setTimeout(() => { this.innerHTML = '<i class="fas fa-shopping-cart"></i> Add'; this.style.background = ''; }, 1500);
            });
        });

        // ── MOBILE NAV ──
        document.getElementById('mobileToggle').addEventListener('click', function() {
            document.getElementById('navLinks').classList.toggle('open');
        });
    </script>
</body>
</html>

