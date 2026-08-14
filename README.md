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

##  GCash QR Code Setup (Manual Payment)

The **Pay via GCash (Manual)** option on the booking page shows guests a QR code + your account name to scan and pay — no typed GCash number. Manage it anytime from **Admin > Settings > GCash QR Code**:
1. Upload a **JPEG (.jpg)** photo/screenshot of your GCash QR (max 5MB)
2. Set the **GCash Account Name** shown under it
3. Save — the booking page updates immediately, no code changes needed


##  Email Notifications (Booking Confirmation)

Guests get an automatic email when their **manual GCash** payment is verified by an admin in **Admin > GCash Payments**. Since InfinityFree blocks outbound SMTP, email is sent via **Brevo's HTTP API** instead of PHP `mail()`.

### 1. Create a Brevo Account
- Go to: https://www.brevo.com
- Sign up free (300 emails/day, no credit card)

### 2. Verify a Sender Email
- In Brevo: **Senders, Domains & Dedicated IPs > Senders**
- Add the email guests will see as the sender
- Click the verification link Brevo sends to that inbox

### 3. Generate an API Key
- In Brevo: **Settings > SMTP & API > API Keys tab**
- Click **Generate a new API key**, copy it (shown only once)

### 4. Set Keys in `includes/config.php`
```php
define('BREVO_API_KEY',      'xkeysib-xxxxxxxxxxxx');
define('BREVO_SENDER_EMAIL', 'yourverified@email.com');
define('BREVO_SENDER_NAME',  'S-Five Inland Resort');
```

### 5. Authorise Your Server's IP
Brevo blocks API calls from unrecognised IPs for security. If you see a `401 unauthorized` error in `logs/mailer.log`, go to https://app.brevo.com/security/authorised_ips and authorise the IP shown in the error. **Do this once for localhost/dev, and again for your live InfinityFree IP when you deploy.**

### 6. How It Works
1. Guest submits a booking and pays via **manual GCash**, uploading a screenshot as proof
2. Admin reviews it in **Admin > GCash Payments** and clicks **Verify Payment**
3. Reservation is marked **Confirmed** + **Paid**
4. `includes/mailer.php` sends a confirmation email (booking code, cottage, dates, total, receipt link) via Brevo
5. Send failures are logged to `logs/mailer.log` — the admin panel also shows an inline warning if the email didn't go out

> Note: PayMongo's auto-confirmed payments (`paymongo_webhook.php`) do **not** currently trigger this email — only manual GCash verification does.

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
- Manual GCash payment (QR code + screenshot proof upload)
- Booking confirmation + track booking page
- Email confirmation sent to guest once manual GCash payment is verified

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
- cURL enabled in php.ini (for PayMongo API and Brevo email)
- A free Brevo account + API key (for email confirmations — see above)
