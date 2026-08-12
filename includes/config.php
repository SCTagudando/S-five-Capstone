<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sfive_resort');

define('SITE_NAME', 'S-Five Inland Resort');
define('SITE_URL', 'http://localhost/sfive');
//API Keys.
define('PAYMONGO_SECRET_KEY',     'sk_test_RRfG4BKw94BxiofgY4dVy8yZ');
define('PAYMONGO_PUBLIC_KEY',     'pk_test_TbV9kiX1LYunLVT4PMqDFMvt');
define('PAYMONGO_WEBHOOK_SECRET', 'whsk_afuS76FLeMokF7DVJ74iUmS5');

// Manual GCash payment details shown to guests on the booking page.
<<<<<<< HEAD
// These are only a fallback used before the `gcash_settings` table has a
// row. The actual QR code image + account name shown to guests are now
// managed live from Admin > Settings (see includes/gcash.php).
=======
// Update these to your resort's actual GCash account.
>>>>>>> 9208a6228cdd386865ccdd24f2211d2488455545
define('GCASH_NUMBER',       '0917-123-4567');
define('GCASH_ACCOUNT_NAME', 'S-Five Inland Resort');

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die('<div style="font-family:sans-serif;padding:2rem;color:red;">
                <h2>Database Connection Error</h2>
                <p>Could not connect to MySQL. Please check your config.php settings.</p>
                <code>' . htmlspecialchars($e->getMessage()) . '</code>
            </div>');
        }
    }
    return $pdo;
}

function generateBookingCode() {
    return 'SFR-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
}

function clean($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

session_start();
?>