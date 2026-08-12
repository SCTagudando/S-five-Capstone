<?php
require_once 'auth.php';
$page_title = 'Payment Proof';
$db = getDB();

$id = (int)($_GET['id'] ?? 0);

$stmt = $db->prepare("
    SELECT g.*, r.booking_code, r.guest_name, r.check_in, r.check_out, c.name AS cottage_name
    FROM gcash_payments g
    JOIN reservations r ON g.reservation_id = r.id
    JOIN cottages c ON r.cottage_id = c.id
    WHERE g.id = ?
");
$stmt->execute([$id]);
$p = $stmt->fetch();

include 'partials/header.php';
?>

<style>
.proof-back-btn {
    display: inline-flex; align-items: center; gap: 0.4rem;
    background: #fff; border: 1.5px solid var(--border); color: var(--text-mid);
    padding: 0.5rem 1rem; border-radius: 8px; font-family: 'Jost', sans-serif;
    font-size: 0.88rem; font-weight: 600; text-decoration: none; cursor: pointer;
}
.proof-back-btn:hover { background: var(--green-mid); border-color: var(--green-mid); color: #fff; }
.proof-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem; }
.proof-section h5 { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #888; margin-bottom: 0.5rem; }
.proof-section p { font-size: 0.85rem; color: #444; margin-bottom: 0.2rem; }
</style>

<a href="gcash.php" class="proof-back-btn">← Back to GCash Payments</a>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings — S-Five Resort</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin.css">
</head>
<?php if (!$p || !$p['proof_image']): ?>
    <div class="card" style="margin-top:1.25rem;">
        <div class="card-body" style="padding:2.5rem;text-align:center;color:#888;">
            Screenshot not found.
        </div>
    </div>
<?php else: ?>
    <div class="card" style="margin-top:1.25rem;">
        <div class="card-header">
            <h3>Proof of Payment <span class="count-badge"><?= htmlspecialchars($p['booking_code']) ?></span></h3>
        </div>
        <div class="card-body">
            <div class="proof-grid">
                <div class="proof-section">
                    <h5>Guest</h5>
                    <p><strong><?= htmlspecialchars($p['guest_name']) ?></strong></p>
                </div>
                <div class="proof-section">
                    <h5>Booking</h5>
                    <p><?= htmlspecialchars($p['cottage_name']) ?></p>
                    <p><?= date('M d', strtotime($p['check_in'])) ?> → <?= date('M d, Y', strtotime($p['check_out'])) ?></p>
                </div>
                <div class="proof-section">
                    <h5>GCash Reference</h5>
                    <p><strong><?= htmlspecialchars($p['reference_number']) ?></strong></p>
                </div>
                <div class="proof-section">
                    <h5>Amount</h5>
                    <p><strong>₱<?= number_format($p['amount'], 2) ?></strong></p>
                </div>
            </div>

            <div style="text-align:center;background:#f8f9fa;border:1px solid var(--border);border-radius:10px;padding:1rem;">
                <img src="../uploads/gcash/<?= htmlspecialchars($p['proof_image']) ?>"
                     alt="GCash payment proof"
                     style="max-width:100%;max-height:75vh;border-radius:8px;">
            </div>

            <a href="gcash.php" class="proof-back-btn" style="margin-top:1.5rem;">← Back to GCash Payments</a>
        </div>
    </div>
<?php endif; ?>

<?php include 'partials/footer.php'; ?>
