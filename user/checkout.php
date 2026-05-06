<?php
session_start();
// Database connection
include('../admin/connect.php');

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
        <h2>⛔ Pehle Register/Login Karo</h2>
        <p>Checkout karne ke liye pehle login karna zaroori hai. Aapka cart safe hai aur login ke baad available rahega.</p>
        <a href="login.php" class="btn-login">🔑 Login Karein</a>
        <a href="registration.php" class="btn-register">➕ Register Karein</a>
    </div></body></html>';
    exit;
}


$cart = json_decode(json_encode(json_decode($_COOKIE['cart'] ?? '[]')));

// Initialize cart from localStorage if empty
if (empty($cart)) {
    $cart = [];
}

// Calculate cart totals
$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += $item->price * $item->quantity;
}

// Shipping charge (free above ₹999)
$shipping = $subtotal >= 999 ? 0 : 99;

// Default COD charge
$codCharge = 0;

// Apply discount if coupon applied
$discount = 0;
if (isset($_SESSION['coupon_discount'])) {
    $discount = $_SESSION['coupon_discount'];
}

$total = $subtotal + $shipping + $codCharge - $discount;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Elite Shoppy</title>
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
            background: var(--light-gray);
            color: var(--black);
        }

        /* ── HEADER ── */
        .checkout-header {
            background: var(--white);
            padding: 20px 0;
            border-bottom: 1px solid #eee;
        }

        .checkout-header .inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .logo-letter {
            background: var(--black);
            color: var(--white);
            font-family: var(--font-display);
            font-size: 28px;
            font-weight: 800;
            width: 40px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-text {
            font-family: var(--font-display);
            font-size: 26px;
            font-weight: 700;
            color: var(--black);
        }

        .secure-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--teal);
            font-size: 14px;
            font-weight: 500;
        }

        /* ── CHECKOUT STEPS ── */
        .checkout-steps {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
            display: flex;
            justify-content: center;
            gap: 0;
        }

        .step {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 25px;
            background: var(--white);
            color: var(--mid-gray);
            font-size: 14px;
            font-weight: 500;
            position: relative;
        }

        .step.active {
            background: var(--teal);
            color: white;
        }

        .step.completed {
            background: var(--teal);
            color: white;
        }

        .step-number {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: rgba(0,196,180,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
        }

        .step.active .step-number,
        .step.completed .step-number {
            background: rgba(255,255,255,0.3);
        }

        .step-arrow {
            color: #ddd;
            font-size: 18px;
        }

        /* ── MAIN CONTENT ── */
        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px 60px;
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
        }

        @media (max-width: 1024px) {
            .checkout-container {
                grid-template-columns: 1fr;
            }
        }

        /* ── FORM SECTIONS ── */
        .checkout-section {
            background: var(--white);
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 20px;
        }

        .section-title {
            font-family: var(--font-display);
            font-size: 20px;
            font-weight: 700;
            color: var(--black);
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--teal);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title i {
            color: var(--teal);
        }

        /* ── FORM FIELDS ── */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: var(--black);
            margin-bottom: 8px;
            display: block;
        }

        .form-label span {
            color: #e74c3c;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            font-family: var(--font-body);
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--teal);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 576px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        /* ── PAYMENT METHODS ── */
        .payment-methods {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .payment-option {
            position: relative;
        }

        .payment-option input {
            position: absolute;
            opacity: 0;
        }

        .payment-option label {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 18px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .payment-option input:checked + label {
            border-color: var(--teal);
            background: rgba(0,196,180,0.05);
        }

        .payment-option label:hover {
            border-color: var(--teal);
        }

        .payment-icon {
            width: 50px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: var(--mid-gray);
        }

        .payment-info {
            flex: 1;
        }

        .payment-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--black);
            margin-bottom: 4px;
        }

        .payment-desc {
            font-size: 12px;
            color: var(--mid-gray);
        }

        .payment-check {
            width: 22px;
            height: 22px;
            border: 2px solid #ddd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .payment-option input:checked ~ .payment-check {
            border-color: var(--teal);
            background: var(--teal);
            color: white;
        }

        .cod-charge {
            display: none;
            margin-top: 15px;
            padding: 15px;
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 6px;
            font-size: 13px;
            color: #856404;
        }

        .cod-charge.show {
            display: block;
        }

        /* ── ORDER SUMMARY ── */
        .order-summary {
            background: var(--white);
            border-radius: 8px;
            padding: 30px;
            position: sticky;
            top: 20px;
        }

        .summary-title {
            font-family: var(--font-display);
            font-size: 20px;
            font-weight: 700;
            color: var(--black);
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--teal);
        }

        .summary-items {
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 20px;
        }

        .summary-item {
            display: flex;
            gap: 15px;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .item-img {
            width: 70px;
            height: 70px;
            border-radius: 6px;
            background: var(--light-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .item-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .item-img i {
            font-size: 24px;
            color: #ccc;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--black);
            margin-bottom: 5px;
        }

        .item-qty {
            font-size: 12px;
            color: var(--mid-gray);
        }

        .item-price {
            font-size: 14px;
            font-weight: 700;
            color: var(--black);
            white-space: nowrap;
        }

        .summary-totals {
            border-top: 2px solid #f0f0f0;
            padding-top: 20px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .total-row span:first-child {
            color: var(--mid-gray);
        }

        .total-row.grand-total {
            font-size: 18px;
            font-weight: 700;
            color: var(--black);
            padding-top: 15px;
            margin-top: 15px;
            border-top: 2px solid #eee;
        }

        .total-row.grand-total span:first-child {
            color: var(--black);
        }

        .cod-badge {
            display: inline-block;
            background: #ffc107;
            color: #000;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 3px;
            margin-left: 5px;
        }

        /* ── COUPON ── */
        .coupon-section {
            margin-bottom: 20px;
        }

        .coupon-form {
            display: flex;
            gap: 10px;
        }

        .coupon-form input {
            flex: 1;
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
        }

        .coupon-form button {
            padding: 10px 20px;
            background: var(--black);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .coupon-form button:hover {
            background: var(--teal);
        }

        .coupon-message {
            margin-top: 10px;
            font-size: 13px;
        }

        .coupon-message.success {
            color: #28a745;
        }

        .coupon-message.error {
            color: #dc3545;
        }

        /* ── PLACE ORDER BUTTON ── */
        .place-order-btn {
            width: 100%;
            padding: 18px;
            background: var(--teal);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 20px;
        }

        .place-order-btn:hover {
            background: var(--teal-dark);
        }

        .place-order-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        /* ── FOOTER ── */
        footer {
            background: #111;
            padding: 30px 20px;
            text-align: center;
        }

        footer p {
            color: rgba(255,255,255,0.5);
            font-size: 13px;
        }

        /* ── PAYMENT FORMS ── */
        .payment-form {
            display: none;
            margin-top: 20px;
            padding: 20px;
            background: var(--light-gray);
            border-radius: 8px;
        }

        .payment-form.show {
            display: block;
        }

        .card-inputs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
    </style>
</head>
<body>

    <!-- ── HEADER ── -->
    <div class="checkout-header">
        <div class="inner">
            <a href="Home.php" class="logo">
                <div class="logo-letter">E</div>
                <div class="logo-text">lite Shoppy</div>
            </a>
            <div class="secure-badge">
                <i class="fas fa-lock"></i>
                <span>100% Secure Checkout</span>
            </div>
        </div>
    </div>

    <!-- ── CHECKOUT STEPS ── -->
    <div class="checkout-steps">
        <div class="step completed">
            <span class="step-number">1</span>
            <span>Cart</span>
        </div>
        <span class="step-arrow"><i class="fas fa-chevron-right"></i></span>
        <div class="step active">
            <span class="step-number">2</span>
            <span>Checkout</span>
        </div>
        <span class="step-arrow"><i class="fas fa-chevron-right"></i></span>
        <div class="step">
            <span class="step-number">3</span>
            <span>Order Complete</span>
        </div>
    </div>

    <!-- ── MAIN CONTENT ── -->
    <div class="checkout-container">
        
        <!-- LEFT SIDE - FORMS -->
        <div class="checkout-forms">
            
            <!-- Shipping Address -->
            <div class="checkout-section">
                <h3 class="section-title">
                    <i class="fas fa-map-marker-alt"></i>
                    Shipping Address
                </h3>
                
                <form id="shippingForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">First Name <span>*</span></label>
                            <input type="text" class="form-control" name="first_name" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Last Name <span>*</span></label>
                            <input type="text" class="form-control" name="last_name" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email Address <span>*</span></label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Phone Number <span>*</span></label>
                        <input type="tel" class="form-control" name="phone" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Address <span>*</span></label>
                        <input type="text" class="form-control" name="address" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">City <span>*</span></label>
                            <input type="text" class="form-control" name="city" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">State <span>*</span></label>
                            <input type="text" class="form-control" name="state" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">PIN Code <span>*</span></label>
                            <input type="text" class="form-control" name="pincode" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Landmark</label>
                            <input type="text" class="form-control" name="landmark">
                        </div>
                    </div>
                </form>
            </div>

            <!-- Payment Method -->
            <div class="checkout-section">
                <h3 class="section-title">
                    <i class="fas fa-credit-card"></i>
                    Payment Method
                </h3>
                
                <div class="payment-methods">
                    
                    <!-- Credit/Debit Card -->
                    <div class="payment-option">
                        <input type="radio" name="payment" id="payment_card" value="card">
                        <label for="payment_card">
                            <div class="payment-icon">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <div class="payment-info">
                                <div class="payment-title">Credit / Debit Card</div>
                                <div class="payment-desc">Visa, Mastercard, RuPay, American Express</div>
                            </div>
                            <div class="payment-check"><i class="fas fa-check"></i></div>
                        </label>
                        <div class="payment-form" id="cardForm">
                            <div class="form-group">
                                <label class="form-label">Card Number</label>
                                <input type="text" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19">
                            </div>
                            <div class="card-inputs">
                                <div class="form-group">
                                    <label class="form-label">Expiry Date</label>
                                    <input type="text" class="form-control" placeholder="MM/YY" maxlength="5">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">CVV</label>
                                    <input type="text" class="form-control" placeholder="123" maxlength="4">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Name on Card</label>
                                <input type="text" class="form-control" placeholder="JOHN DOE">
                            </div>
                        </div>
                    </div>

                    <!-- UPI -->
                    <div class="payment-option">
                        <input type="radio" name="payment" id="payment_upi" value="upi">
                        <label for="payment_upi">
                            <div class="payment-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <div class="payment-info">
                                <div class="payment-title">UPI / Google Pay / PhonePe</div>
                                <div class="payment-desc">Pay using UPI ID or linked app</div>
                            </div>
                            <div class="payment-check"><i class="fas fa-check"></i></div>
                        </label>
                        <div class="payment-form" id="upiForm">
                            <div class="form-group">
                                <label class="form-label">UPI ID</label>
                                <input type="text" class="form-control" placeholder="yourname@upi">
                            </div>
                        </div>
                    </div>

                    <!-- Net Banking -->
                    <div class="payment-option">
                        <input type="radio" name="payment" id="payment_netbanking" value="netbanking">
                        <label for="payment_netbanking">
                            <div class="payment-icon">
                                <i class="fas fa-university"></i>
                            </div>
                            <div class="payment-info">
                                <div class="payment-title">Net Banking</div>
                                <div class="payment-desc">All major Indian banks supported</div>
                            </div>
                            <div class="payment-check"><i class="fas fa-check"></i></div>
                        </label>
                        <div class="payment-form" id="netbankingForm">
                            <div class="form-group">
                                <label class="form-label">Select Bank</label>
                                <select class="form-control">
                                    <option>Select Bank</option>
                                    <option>HDFC Bank</option>
                                    <option>ICICI Bank</option>
                                    <option>State Bank of India</option>
                                    <option>Axis Bank</option>
                                    <option>Kotak Bank</option>
                                    <option>Yes Bank</option>
                                    <option> Punjab National Bank</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Paytm -->
                    <div class="payment-option">
                        <input type="radio" name="payment" id="payment_paytm" value="paytm">
                        <label for="payment_paytm">
                            <div class="payment-icon" style="color: #00baf2;">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="payment-info">
                                <div class="payment-title">Paytm</div>
                                <div class="payment-desc">Pay via Paytm wallet or QR</div>
                            </div>
                            <div class="payment-check"><i class="fas fa-check"></i></div>
                        </label>
                        <div class="payment-form" id="paytmForm">
                            <div class="form-group">
                                <label class="form-label">Paytm Mobile Number</label>
                                <input type="tel" class="form-control" placeholder="Enter 10-digit mobile number">
                            </div>
                        </div>
                    </div>

                    <!-- Amazon Pay -->
                    <div class="payment-option">
                        <input type="radio" name="payment" id="payment_amazon" value="amazon">
                        <label for="payment_amazon">
                            <div class="payment-icon" style="color: #ff9900;">
                                <i class="fab fa-amazon"></i>
                            </div>
                            <div class="payment-info">
                                <div class="payment-title">Amazon Pay</div>
                                <div class="payment-desc">Pay using Amazon Pay balance</div>
                            </div>
                            <div class="payment-check"><i class="fas fa-check"></i></div>
                        </label>
                    </div>

                    <!-- COD -->
                    <div class="payment-option">
                        <input type="radio" name="payment" id="payment_cod" value="cod">
                        <label for="payment_cod">
                            <div class="payment-icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div class="payment-info">
                                <div class="payment-title">Cash on Delivery <span class="cod-badge">+₹49</span></div>
                                <div class="payment-desc">Pay when you receive the product</div>
                            </div>
                            <div class="payment-check"><i class="fas fa-check"></i></div>
                        </label>
                        <div class="cod-charge" id="codCharge">
                            <i class="fas fa-info-circle"></i>
                            <strong>Cash on Delivery Charge: ₹49</strong><br>
                            This charge applies to cover the additional handling and delivery verification costs.
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <!-- RIGHT SIDE - ORDER SUMMARY -->
        <div class="order-summary">
            <h3 class="summary-title">Order Summary</h3>
            
            <!-- Coupon -->
            <div class="coupon-section">
                <div class="coupon-form">
                    <input type="text" id="couponCode" placeholder="Enter coupon code">
                    <button type="button" onclick="applyCoupon()">Apply</button>
                </div>
                <div class="coupon-message" id="couponMessage"></div>
            </div>

            <!-- Items -->
            <div class="summary-items">
                <?php if (!empty($cart)): ?>
                    <?php foreach ($cart as $item): ?>
                        <div class="summary-item">
                            <div class="item-img">
                                <?php
                                $imgPath = '../admin/images/' . htmlspecialchars($item->photo);
                                if (!empty($item->photo) && file_exists($imgPath)):
                                ?>
                                    <img src="<?php echo $imgPath; ?>" alt="<?php echo htmlspecialchars($item->name); ?>">
                                <?php else: ?>
                                    <i class="fas fa-tshirt"></i>
                                <?php endif; ?>
                            </div>
                            <div class="item-details">
                                <div class="item-name"><?php echo htmlspecialchars($item->name); ?></div>
                                <div class="item-qty">Qty: <?php echo $item->quantity; ?></div>
                            </div>
                            <div class="item-price">₹<?php echo number_format($item->price * $item->quantity, 0); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; color: #888; padding: 20px;">Your cart is empty</p>
                <?php endif; ?>
            </div>

            <!-- Totals -->
            <div class="summary-totals">
                <div class="total-row">
                    <span>Subtotal</span>
                    <span>₹<?php echo number_format($subtotal, 0); ?></span>
                </div>
                <div class="total-row">
                    <span>Shipping <?php echo $shipping == 0 ? '<span style="color:#28a745">(Free)</span>' : ''; ?></span>
                    <span><?php echo $shipping == 0 ? '₹0' : '₹' . number_format($shipping, 0); ?></span>
                </div>
                <div class="total-row" id="codRow" style="display: none;">
                    <span>COD Charge</span>
                    <span id="codAmount">₹0</span>
                </div>
                <div class="total-row" id="discountRow" style="display: <?php echo $discount > 0 ? 'flex' : 'none'; ?>;">
                    <span>Discount</span>
                    <span style="color: #28a745;">-₹<?php echo number_format($discount, 0); ?></span>
                </div>
                <div class="total-row grand-total">
                    <span>Total</span>
                    <span id="grandTotal">₹<?php echo number_format($total, 0); ?></span>
                </div>
            </div>

            <button class="place-order-btn" onclick="placeOrder()" id="placeOrderBtn">
                <i class="fas fa-lock"></i> Place Order
            </button>

            <p style="text-align: center; margin-top: 15px; font-size: 12px; color: #888;">
                <i class="fas fa-shield-alt"></i> Safe & Secure Payments
            </p>
        </div>
    </div>

    <!-- ── FOOTER ── -->
    <footer>
        <p>&copy; 2025 Elite Shoppy. All rights reserved.</p>
    </footer>

    <script>
        // Cart data from localStorage
        let cart = JSON.parse(localStorage.getItem('eliteshoppy_cart')) || [];
        
        let codCharge = 0;
        let discount = 0;
        let subtotal = <?php echo $subtotal; ?>;
        let shipping = <?php echo $shipping; ?>;
        
        // Update totals
        function updateTotals() {
            let total = subtotal + shipping + codCharge - discount;
            document.getElementById('grandTotal').textContent = '₹' + total.toLocaleString();
            document.getElementById('codAmount').textContent = '₹' + codCharge;
            
            if (codCharge > 0) {
                document.getElementById('codRow').style.display = 'flex';
            } else {
                document.getElementById('codRow').style.display = 'none';
            }
        }
        
        // Payment method selection
        const paymentOptions = document.querySelectorAll('input[name="payment"]');
        paymentOptions.forEach(option => {
            option.addEventListener('change', function() {
                // Hide all payment forms
                document.querySelectorAll('.payment-form').forEach(form => form.classList.remove('show'));
                document.getElementById('codCharge').classList.remove('show');
                
                // Show selected payment form
                if (this.value === 'card') {
                    document.getElementById('cardForm').classList.add('show');
                    codCharge = 0;
                } else if (this.value === 'upi') {
                    document.getElementById('upiForm').classList.add('show');
                    codCharge = 0;
                } else if (this.value === 'netbanking') {
                    document.getElementById('netbankingForm').classList.add('show');
                    codCharge = 0;
                } else if (this.value === 'paytm') {
                    document.getElementById('paytmForm').classList.add('show');
                    codCharge = 0;
                } else if (this.value === 'amazon') {
                    codCharge = 0;
                } else if (this.value === 'cod') {
                    document.getElementById('codCharge').classList.add('show');
                    codCharge = 49;
                }
                
                updateTotals();
            });
        });
        
        // Coupon functionality
        function applyCoupon() {
            const code = document.getElementById('couponCode').value.toUpperCase();
            const messageEl = document.getElementById('couponMessage');
            
            // Define valid coupons
            const coupons = {
                'ELITE100': { type: 'percent', value: 100, min: 500 },
                'SAVE50': { type: 'flat', value: 50, min: 300 },
                'NEWUSER': { type: 'percent', value: 150, min: 0 },
                'FESTIVE200': { type: 'flat', value: 200, min: 1000 }
            };
            
            if (coupons[code]) {
                const coupon = coupons[code];
                if (subtotal >= coupon.min) {
                    if (coupon.type === 'percent') {
                        discount = (subtotal * coupon.value) / 100;
                    } else {
                        discount = coupon.value;
                    }
                    messageEl.className = 'coupon-message success';
                    messageEl.textContent = 'Coupon applied! You saved ₹' + discount.toLocaleString();
                    document.getElementById('discountRow').style.display = 'flex';
                } else {
                    messageEl.className = 'coupon-message error';
                    messageEl.textContent = 'Minimum order value ₹' + coupon.min + ' required';
                    discount = 0;
                }
            } else {
                messageEl.className = 'coupon-message error';
                messageEl.textContent = 'Invalid coupon code';
                discount = 0;
            }
            
            updateTotals();
        }
        
        // Place order
        function placeOrder() {
            const paymentMethod = document.querySelector('input[name="payment"]:checked');
            const firstName = document.querySelector('input[name="first_name"]').value;
            const email = document.querySelector('input[name="email"]').value;
            const phone = document.querySelector('input[name="phone"]').value;
            const address = document.querySelector('input[name="address"]').value;
            const city = document.querySelector('input[name="city"]').value;
            const pincode = document.querySelector('input[name="pincode"]').value;
            
            if (!firstName || !email || !phone || !address || !city || !pincode) {
                alert('Please fill all required fields');
                return;
            }
            
            if (!paymentMethod) {
                alert('Please select a payment method');
                return;
            }
            
            if (cart.length === 0) {
                alert('Your cart is empty');
                return;
            }
            
            // Calculate final total
            let total = subtotal + shipping + codCharge - discount;
            
            // Simulate order placement
            alert('Order placed successfully!\n\n' +
                  'Payment Method: ' + paymentMethod.value.toUpperCase() + 
                  (codCharge > 0 ? '\nCOD Charge: ₹' + codCharge : '') +
                  '\nTotal Paid: ₹' + total.toLocaleString() +
                  '\n\nYour order will be delivered within 5-7 business days.');
            
            // Clear cart
            localStorage.removeItem('eliteshoppy_cart');
            window.location.href = 'Home.php';
        }
        
        // Initial calculation
        updateTotals();
    </script>
</body>
</html>

