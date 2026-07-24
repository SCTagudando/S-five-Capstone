<?php
// view_proof.php — Guest-facing view of their own GCash payment proof screenshot.
// Access is gated the same way as check_booking.php: the booking code acts
// as the lookup key, so only someone who has the code can view the proof.
require_once 'includes/config.php';
$db = getDB();

$code = clean($_GET['code'] ?? '');
$proof = null;
$reservation = null;

if ($code) {
    $stmt = $db->prepare("
        SELECT r.id, r.booking_code, r.guest_name, r.payment_method
        FROM reservations r
        WHERE r.booking_code = ?
    ");
    $stmt->execute([$code]);
    $reservation = $stmt->fetch();

    if ($reservation) {
        $gstmt = $db->prepare("
            SELECT reference_number, amount, proof_image, status, submitted_at
            FROM gcash_payments
            WHERE reservation_id = ?
            ORDER BY id DESC LIMIT 1
        ");
        $gstmt->execute([$reservation['id']]);
        $proof = $gstmt->fetch();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Proof — S-Five Inland Resort</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<nav class="navbar navbar-light" id="navbar">
    <div class="nav-container">
        <a href="index.php" class="nav-logo"><img src="images/sfive_logo.jpg" alt="S-Five Inland Resort" class="nav-logo-img"></a>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="check_booking.php" class="btn-nav">My Booking</a></li>
        </ul>
    </div>
</nav>

<div class="booking-page">
    <div class="booking-hero">
        <h1>Your <em>Payment Proof</em></h1>
        <p>The screenshot you submitted for booking <?= $reservation ? '<strong>'.htmlspecialchars($reservation['booking_code']).'</strong>' : '' ?>.</p>
    </div>

    <div class="container" style="max-width:680px;">

        <a href="check_booking.php?code=<?= urlencode($code) ?>" class="btn-ghost" style="display:inline-flex;align-items:center;gap:0.4rem;margin-bottom:1.25rem;">← Back to My Booking</a>

        <?php if (!$reservation): ?>
        <div class="alert-error">No reservation found with that booking code.</div>
        <?php elseif (!$proof || !$proof['proof_image']): ?>
        <div class="alert-error">No payment screenshot was found for this booking.</div>
        <?php else: ?>

        <div class="success-card">
            <div class="booking-summary-box">
                <div class="summary-row"><span>Reference #</span><strong><?= htmlspecialchars($proof['reference_number']) ?></strong></div>
                <div class="summary-row"><span>Amount</span><strong>₱<?= number_format($proof['amount'], 2) ?></strong></div>
                <div class="summary-row"><span>Status</span><strong><span class="status-badge <?= ['Pending'=>'pending','Verified'=>'confirmed','Rejected'=>'cancelled'][$proof['status']] ?? 'pending' ?>"><?= htmlspecialchars($proof['status']) ?></span></strong></div>
                <div class="summary-row"><span>Submitted</span><strong><?= date('F d, Y g:i A', strtotime($proof['submitted_at'])) ?></strong></div>
            </div>

            <div style="text-align:center;background:#f8f9fa;border:1px solid #e5e8e5;border-radius:10px;padding:1rem;margin-top:1rem;">
                <img src="uploads/gcash/<?= htmlspecialchars($proof['proof_image']) ?>"
                     alt="GCash payment proof"
                     style="max-width:100%;max-height:70vh;border-radius:8px;">
            </div>

            <div class="success-actions">
                <a href="check_booking.php?code=<?= urlencode($code) ?>" class="btn-primary">← Back to My Booking</a>
                <a href="index.php" class="btn-ghost">Back to Home</a>
            </div>
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