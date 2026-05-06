# User Profile Feature - Implementation Plan

## ✅ Completed
- [x] Created TODO.md
- [x] Database tables (run `php admin/setup_database.php`)
- [x] New files: profile.php, logout.php, add_wishlist.php
- [x] Home.php: session + profile dropdown + wishlist full integration

## 🔄 In Progress

## ⏳ Pending Steps
1. **Database Setup** 
   - Create `wishlist` table
   - Create `orders` & `order_items` tables
   - Update admin/setup_database.php

2. **New PHP Files**
   - `user/profile.php` (dashboard: details, wishlist, orders)
   - `user/logout.php`
   - `user/add_wishlist.php` (AJAX handler)

3. **Header Updates** (ALL user/*.php)
   - Add `session_start()`
   - User data fetch query
   - Dynamic profile dropdown in `.top-links`
   - Replace 'Sign In' → profile avatar

4. **Wishlist Integration**
   - Add heart icons to products (Home.php, category.php, etc.)
   - JS toggle + AJAX to add_wishlist.php

5. **Orders History**
   - Query/display in profile.php
   - Link from dropdown

6. **CSS/JS Polish**
   - Dropdown animations
   - Mobile responsive
   - Wishlist count badge

7. **Testing**
   - Login flow
   - Profile dropdown
   - Wishlist add/remove
   - Orders display
   - Logout

## 📁 Files to Update
- user/Home.php, cart.php, checkout.php, category.php, about.php, contact.php, brand.php, subcategory.php (headers)
- admin/setup_database.php

**Current Step: 1 - Database Setup**

