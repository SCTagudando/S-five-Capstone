# S-Five Inland Resort — Complete Booking System
## PHP + MySQL | PayMongo GCash API | Admin Panel

### Step 1 — Create the Database
- Open **phpMyAdmin**
- Import `database.sql`
- This creates the `sfive_resort` database + all tables + 12 sample cottages

### Step 2 — Configure `includes/config.php`
php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sfive_resort');
define('SITE_URL', 'http://localhost/sfive');

// PayMongo keys (get from dashboard.paymongo.com)
define('PAYMONGO_SECRET_KEY',     'sk_test_YOUR_KEY_HERE');
define('PAYMONGO_PUBLIC_KEY',     'pk_test_YOUR_KEY_HERE');
define('PAYMONGO_WEBHOOK_SECRET', 'whsk_YOUR_WEBHOOK_SECRET');

### Step 3 — Place Files
Copy `sfive/` into your web root:
- **XAMPP**: `C:/xampp/htdocs/sfive/`
- **WAMP**: `C:/wamp64/www/sfive/`

### Step 4 — Open in Browser
http://localhost/sfive/

**Admin Panel:**

http://localhost/sfive/admin/login.php
Email: admin@sfive.com
Password: password


---

##  PayMongo GCash Setup

### 1. Create a PayMongo Account
- Go to: https://dashboard.paymongo.com
- Sign up for a free account
- Get your **Test API Keys** (for development)
- Get **Live API Keys** when going live

### 2. Set Your API Keys in `includes/config.php`
define('PAYMONGO_SECRET_KEY',     'sk_test_xxxxxxxxxxxx');
define('PAYMONGO_PUBLIC_KEY',     'pk_test_xxxxxxxxxxxx');
define('PAYMONGO_WEBHOOK_SECRET', 'whsk_xxxxxxxxxxxx');

### 3. Register the Webhook (for auto-confirmation)
In your PayMongo Dashboard > Webhooks, add:
URL:https://yourdomain.com/sfive/paymongo_webhook.php 
Events: payment.paid, link.payment.paid

### 4. How It Works
1. Customer selects **Pay via GCash** on booking form
2. After submitting, a **PayMongo Payment Link** is created instantly
3. Customer clicks **"Pay via GCash Now"** → redirected to secure PayMongo page
4. Customer pays via GCash on their phone
5. PayMongo sends a webhook to your server
6. Reservation is **automatically Confirmed + marked Paid** 

---

##  Replacing Sample Photos

### Quick Method
Upload real photos to the `images/` folder named:
- `bahay_kubo_1.jpg`, `bahay_kubo_2.jpg` ... etc.

Then update `cottage.php` gallery section to point to real images.

### How the Placeholder Works
`images/cottage_placeholder.php` auto-generates SVG illustrations per cottage type:
- **Bahay Kubo** → green bamboo hut scene
- **Open Cottage** → open pavilion scene  
- **Kubo Premium** → luxury kubo scene

Each has a watermark: _"Sample image — replace with actual photo"_

---

##  Cottage Types

| Type | Count | Price | Features |
|------|-------|-------|---------|
| Bahay Kubo 1–5 | 5 | ₱800/night | Fan, veranda, garden |
| Open Cottage 1–2 | 2 | ₱2,800–3,500 | 40–60 guests, events |
| Kubo Premium 1–5 | 5 | ₱2,500–4,000 | Aircon, good beds |

---

##  Features

**Customer Side**
- Homepage with live availability checker
- Clickable cottage thumbnails → detail page with photo gallery + lightbox
- Reservation form with real-time price calculator
- PayMongo GCash API payment (auto-confirms on payment)
- Booking confirmation + track booking page

**Admin Panel**
- Dashboard with stats + monthly bookings chart
- Reservation management (approve/reject/cancel)
- Cottage management (add/edit/delete/hide)
- Guest records
- Monthly revenue reports (printable)
- GCash payment verification panel

---

##  Requirements
- PHP 7.4+
- MySQL 5.7+ / MariaDB 10+
- XAMPP / WAMP / any PHP server
- cURL or `allow_url_fopen = On` in php.ini (for PayMongo API)
