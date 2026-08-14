<?php
// check_booking.php - Guest can look up their reservation
require_once 'includes/config.php';
require_once 'includes/paymongo.php';

$db = getDB();
$reservation = null;
$cottage = null;
$error = '';
$cancel_msg = '';

$code = clean($_GET['code'] ?? $_POST['code'] ?? '');

// ---- Handle self-service cancellation ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking'])) {
    $cancel_code = clean($_POST['code'] ?? '');
    $stmt = $db->prepare("SELECT * FROM reservations WHERE booking_code = ?");
    $stmt->execute([$cancel_code]);
    $toCancel = $stmt->fetch();

    if (!$toCancel) {
        $_SESSION['cancel_msg'] = "error:Booking not found.";
    } elseif ($toCancel['status'] === 'Cancelled') {
        $_SESSION['cancel_msg'] = "error:This booking has already been cancelled.";
    } else {
        $hoursSinceBooked = (time() - strtotime($toCancel['created_at'])) / 3600;
        if ($hoursSinceBooked > 24) {
            $_SESSION['cancel_msg'] = "error:The 24-hour cancellation window for this booking has passed. Please contact the resort directly.";
        } else {
            // Preserve payment history: a booking that was already Paid stays
            // Paid so the admin knows a refund is owed, instead of silently
            // flipping to Unpaid and losing that fact.
            $newPaymentStatus = $toCancel['payment_status'] === 'Paid' ? 'Paid' : 'Unpaid';
            $db->prepare("UPDATE reservations
                          SET status='Cancelled', payment_status=?, cancelled_by='Guest', cancelled_at=NOW()
                          WHERE id=?")
               ->execute([$newPaymentStatus, $toCancel['id']]);
            $_SESSION['cancel_msg'] = "success:Your booking has been cancelled.";
        }
    }

    header('Location: check_booking.php?code=' . urlencode($cancel_code));
    exit;
}

if (isset($_SESSION['cancel_msg'])) {
    $cancel_msg = $_SESSION['cancel_msg'];
    unset($_SESSION['cancel_msg']);
}

if ($code) {
    $stmt = $db->prepare("
        SELECT r.*, c.name AS cottage_name, c.price_per_night, c.description AS cottage_desc
        FROM reservations r
        JOIN cottages c ON r.cottage_id = c.id
        WHERE r.booking_code = ?
    ");
    $stmt->execute([$code]);
    $reservation = $stmt->fetch();
    if (!$reservation) $error = "No reservation found with code: <strong>" . htmlspecialchars($code) . "</strong>";

    // GCash proof (manual payments) — used to show a "View My Payment Proof" link
    $gcash_proof = null;
    if ($reservation && $reservation['payment_method'] === 'GCash') {
        $gstmt = $db->prepare("SELECT proof_image, status FROM gcash_payments WHERE reservation_id = ? ORDER BY id DESC LIMIT 1");
        $gstmt->execute([$reservation['id']]);
        $gcash_proof = $gstmt->fetch();
    }

    if ($reservation
        && $reservation['payment_method'] === 'GCash Online'
        && $reservation['payment_status'] !== 'Paid'
        && !empty($reservation['paymongo_link_id'])
    ) {
        $pm = getPaymongoLinkStatus($reservation['paymongo_link_id']);

        if ($pm['success'] && $pm['paid']) {
            // Payment confirmed by polling — update DB now
            $db->prepare("
                UPDATE reservations
                SET status = 'Confirmed', payment_status = 'Paid'
                WHERE booking_code = ?
            ")->execute([$code]);

            $db->prepare("
                INSERT INTO gcash_payments
                    (reservation_id, reference_number, amount, sender_name, sender_number, status)
                VALUES (?, ?, ?, 'GCash via PayMongo', 'PayMongo Poll', 'Verified')
                ON DUPLICATE KEY UPDATE status='Verified', verified_at=NOW()
            ")->execute([$reservation['id'], $pm['payment_id'], $pm['amount']]);

            // Refresh reservation so page shows updated status
            $stmt->execute([$code]);
            $reservation = $stmt->fetch();

            // Log it
            $logDir = __DIR__ . '/logs/';
            if (!is_dir($logDir)) mkdir($logDir, 0755, true);
            file_put_contents($logDir . 'webhook.log',
                date('Y-m-d H:i:s') . " | ✅ POLLED CONFIRMED | {$code} | ₱{$pm['amount']}\n",
                FILE_APPEND
            );
        } elseif ($pm['success'] && !$pm['paid'] && in_array($pm['status'], ['expired', 'unknown'], true)) {
            // Link expired (or PayMongo no longer recognizes it) — regenerate a fresh one
            // so the guest can still resume payment from this tracking page.
            $nights = max(1, (strtotime($reservation['check_out']) - strtotime($reservation['check_in'])) / 86400);

            $pm_new = createPaymongoGcashLink([
                'amount'        => (int)round($reservation['total_price'] * 100),
                'description'   => $reservation['cottage_name'] . ' (' . $nights . ' night' . ($nights > 1 ? 's' : '') . ')',
                'booking_code'  => $reservation['booking_code'],
                'customer_name' => $reservation['guest_name'],
                'email'         => $reservation['guest_email'],
                'phone'         => $reservation['guest_phone'],
            ]);

            if ($pm_new['success']) {
                $db->prepare("UPDATE reservations SET paymongo_link_id=?, paymongo_checkout_url=? WHERE id=?")
                   ->execute([$pm_new['link_id'], $pm_new['checkout_url'], $reservation['id']]);

                // Refresh reservation so the page shows the new link
                $stmt->execute([$code]);
                $reservation = $stmt->fetch();
            }
        }
    }
}

[$cancel_msg_type, $cancel_msg_text] = $cancel_msg ? explode(':', $cancel_msg, 2) : ['', ''];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Booking — S-Five Inland Resort</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<nav class="navbar navbar-light" id="navbar">
    <div class="nav-container">
        <a href="index.php" class="nav-logo">
            <img src="images/sfive_logo.jpg" alt="S-Five Inland Resort" class="nav-logo-img">
            <span class="logo-text">S-Five Inland Resort</span>
        </a>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="booking.php" class="btn-nav">Book Now</a></li>
        </ul>
        <button class="nav-toggle" id="navToggle">☰</button>
    </div>
</nav>
    </div>
</nav>

<div class="booking-page">
    <div class="booking-hero">
        <h1>Track Your <em>Reservation</em></h1>
        <p>Enter your booking code to check your reservation status.</p>
    </div>

    <div class="container" style="max-width:680px;">

        <!-- CANCELLATION RESULT -->
        <?php if ($cancel_msg_text): ?>
        <div class="alert-<?= $cancel_msg_type === 'success' ? 'error' : 'error' ?>" style="<?= $cancel_msg_type==='success' ? 'background:#d1e7dd;border-color:#a3cfbb;color:#0a5c36;' : '' ?>">
            <?= htmlspecialchars($cancel_msg_text) ?>
        </div>
        <?php endif; ?>

        <!-- SEARCH FORM -->
        <form method="GET" class="lookup-form">
            <div class="lookup-row">
                <input type="text" name="code" placeholder="e.g. SFR-AB12CD34"
                       value="<?= htmlspecialchars($code) ?>"
                       style="text-transform:uppercase;" required>
                <button type="submit" class="btn-primary">Search</button>
            </div>
        </form>

        <!-- ERROR -->
        <?php if ($error): ?>
        <div class="alert-error"><?= $error ?></div>
        <?php endif; ?>

        <!-- RESERVATION RESULT -->
        <?php if ($reservation): ?>
        <?php
        $statusClass = strtolower($reservation['status']);
        $statusEmoji = ['pending'=>'⏳','confirmed'=>'✅','cancelled'=>'❌'][$statusClass] ?? '📋';
        $nights = (strtotime($reservation['check_out']) - strtotime($reservation['check_in'])) / 86400;
        ?>
        <div class="success-card">
            <div class="success-icon"><?= $statusEmoji ?></div>
            <h2><?= $reservation['booking_code'] ?></h2>
            <p>Booking status: <span class="status-badge <?= $statusClass ?>"><?= $reservation['status'] ?></span></p>

            <div class="booking-summary-box">
                <div class="summary-row"><span>Guest Name</span><strong><?= htmlspecialchars($reservation['guest_name']) ?></strong></div>
                <div class="summary-row"><span>Cottage</span><strong><?= htmlspecialchars($reservation['cottage_name']) ?></strong></div>
                <div class="summary-row"><span>Check-in</span><strong><?= date('F d, Y', strtotime($reservation['check_in'])) ?></strong></div>
                <div class="summary-row"><span>Check-out</span><strong><?= date('F d, Y', strtotime($reservation['check_out'])) ?></strong></div>
                <div class="summary-row"><span>Duration</span><strong><?= $nights ?> night(s)</strong></div>
                <div class="summary-row"><span>Guests</span><strong><?= $reservation['num_guests'] ?></strong></div>
                <div class="summary-row"><span>Payment</span><strong><?= $reservation['payment_status'] ?></strong></div>
                <div class="summary-row total-row"><span>Total</span><strong>₱<?= number_format($reservation['total_price'], 2) ?></strong></div>
            </div>

            <?php if ($reservation['special_requests']): ?>
            <p class="note-text">📝 Special requests: <?= htmlspecialchars($reservation['special_requests']) ?></p>
            <?php endif; ?>

            <p class="note-text">📅 Booked on: <?= date('F d, Y g:i A', strtotime($reservation['created_at'])) ?></p>

            <?php
            $hoursSinceBooked = (time() - strtotime($reservation['created_at'])) / 3600;
            $canCancel = $reservation['status'] !== 'Cancelled' && $hoursSinceBooked <= 24;
            $hoursLeft = max(0, 24 - $hoursSinceBooked);
            ?>
            <?php if ($canCancel): ?>
            <p class="note-text">⏱️ Free cancellation available for <?= floor($hoursLeft) ?>h <?= floor(($hoursLeft - floor($hoursLeft)) * 60) ?>m more.</p>
            <?php elseif ($reservation['status'] !== 'Cancelled'): ?>
            <p class="note-text">⏱️ The 24-hour cancellation window has passed. Please contact the resort to make changes.</p>
            <?php endif; ?>

            <?php if ($reservation['payment_method'] === 'GCash Online'
                      && $reservation['payment_status'] !== 'Paid'
                      && $reservation['status'] !== 'Cancelled'
                      && !empty($reservation['paymongo_checkout_url'])): ?>
            <div class="alert-error" style="background:#eaf6ff;border-color:#b6e0fe;color:#0a4a7a;">
                💳 Your booking is still <strong>pending payment</strong>. If you closed the checkout page before finishing, you can resume it below.
            </div>
            <?php endif; ?>

            <div class="success-actions">
                <a href="receipt.php?code=<?= $reservation['booking_code'] ?>" class="btn-primary" target="_blank">🧾 View Receipt</a>
                <?php if ($reservation['payment_method'] === 'GCash Online'
                          && $reservation['payment_status'] !== 'Paid'
                          && $reservation['status'] !== 'Cancelled'
                          && !empty($reservation['paymongo_checkout_url'])): ?>
                <a href="<?= htmlspecialchars($reservation['paymongo_checkout_url']) ?>" class="btn-primary" target="_blank" style="background:#0072CE;border-color:#0072CE;">
                    💳 Pay Now
                </a>
                <?php endif; ?>
                <?php if (!empty($gcash_proof) && !empty($gcash_proof['proof_image'])): ?>
                <a href="view_proof.php?code=<?= urlencode($reservation['booking_code']) ?>" class="btn-ghost">📷 View My Payment Proof</a>
                <?php endif; ?>
                <a href="index.php" class="btn-ghost">Back to Home</a>
                <a href="booking.php" class="btn-ghost">New Booking</a>
            </div>

            <?php if ($canCancel): ?>
            <form method="POST" style="margin-top:1rem;"
                  onsubmit="return confirm('Cancel this booking? This cannot be undone.');">
                <input type="hidden" name="code" value="<?= htmlspecialchars($reservation['booking_code']) ?>">
                <button type="submit" name="cancel_booking" value="1"
                        style="background:#fff5f5;border:1.5px solid #f5c6cb;color:#842029;padding:0.7rem 1.5rem;border-radius:8px;font-family:'Jost',sans-serif;font-size:0.9rem;font-weight:600;cursor:pointer;">
                    ❌ Cancel Booking
                </button>
            </form>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if (!$code && !$reservation): ?>
        <div class="empty-state">
            <div style="font-size:4rem;">🔍</div>
            <p>Enter your booking code above to find your reservation.</p>
            <p><a href="booking.php">Don't have a booking yet? Reserve now →</a></p>
        </div>
        <?php endif; ?>

    </div>
</div>

<footer class="footer">
    <div class="footer-bottom"><p>&copy; <?= date('Y') ?> S-Five Inland Resort.</p></div>
</footer>
<script src="js/main.js"></script>
</body>
</html>