<?php
<<<<<<< HEAD
// admin/settings.php — Admin account settings (change password) + GCash QR settings
require_once 'auth.php';
require_once '../includes/gcash.php';
=======
// admin/settings.php — Admin account settings (change password)
require_once 'auth.php';
>>>>>>> 9208a6228cdd386865ccdd24f2211d2488455545
$page_title = 'Settings';
$db  = getDB();
$msg = '';

<<<<<<< HEAD
$form = $_POST['form'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $form === 'password') {
=======
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
>>>>>>> 9208a6228cdd386865ccdd24f2211d2488455545
    $current_password = $_POST['current_password'] ?? '';
    $new_password     = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND role = 'admin'");
    $stmt->execute([$_SESSION['admin_id']]);
    $admin = $stmt->fetch();

    if (!$admin || !password_verify($current_password, $admin['password'])) {
        $msg = "error:Current password is incorrect.";
    } elseif (strlen($new_password) < 6) {
        $msg = "error:New password must be at least 6 characters long.";
    } elseif ($new_password !== $confirm_password) {
        $msg = "error:New password and confirmation do not match.";
    } elseif (password_verify($new_password, $admin['password'])) {
        $msg = "error:New password must be different from your current password.";
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $db->prepare("UPDATE users SET password = ? WHERE id = ?")
           ->execute([$hashed, $admin['id']]);
        $msg = "success:Password updated successfully.";
    }
}

<<<<<<< HEAD
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $form === 'gcash') {
    $account_name = clean($_POST['gcash_account_name'] ?? '');
    if ($account_name === '') $account_name = 'S-Five Inland Resort';

    $file = (!empty($_FILES['gcash_qr']['name'])) ? $_FILES['gcash_qr'] : null;
    $result = saveGcashSettings($db, $account_name, $file);

    $msg = $result['success']
        ? "success:GCash payment details updated successfully."
        : "error:" . $result['error'];
}

$gcash_settings = getGcashSettings($db);

=======
>>>>>>> 9208a6228cdd386865ccdd24f2211d2488455545
[$msg_type, $msg_text] = $msg ? explode(':', $msg, 2) : ['', ''];
include 'partials/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings — S-Five Resort</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin.css">
</head>
<?php if ($msg_text): ?>
<div class="alert-<?= $msg_type ?>"><?= htmlspecialchars($msg_text) ?></div>
<?php endif; ?>

<div class="card" style="max-width:520px;">
    <div class="card-header">
        <h3>Change Password</h3>
    </div>
    <div class="card-body">
        <form method="POST" class="admin-form">
<<<<<<< HEAD
            <input type="hidden" name="form" value="password">
=======
>>>>>>> 9208a6228cdd386865ccdd24f2211d2488455545
            <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="current_password" placeholder="••••••••" required autocomplete="current-password">
            </div>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" placeholder="At least 6 characters" required minlength="6" autocomplete="new-password">
            </div>
            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" placeholder="Re-enter new password" required minlength="6" autocomplete="new-password">
            </div>
            <button type="submit" class="btn-save">Update Password</button>
        </form>
    </div>
</div>

<<<<<<< HEAD
<div class="card" style="max-width:520px;margin-top:1.5rem;">
    <div class="card-header">
        <h3>GCash QR Code</h3>
    </div>
    <div class="card-body">
        <p style="font-size:0.85rem;color:#666;margin-bottom:1rem;">
            This QR code and account name are shown to guests on the booking page for manual GCash payments.
            Guests scan it in their GCash app and enter the amount themselves — update it any time your QR changes.
        </p>

        <?php if (!empty($gcash_settings['qr_image']) && file_exists(__DIR__ . '/../uploads/gcash/' . $gcash_settings['qr_image'])): ?>
        <div style="text-align:center;margin-bottom:1.25rem;">
            <img src="../uploads/gcash/<?= htmlspecialchars($gcash_settings['qr_image']) ?>?v=<?= filemtime(__DIR__ . '/../uploads/gcash/' . $gcash_settings['qr_image']) ?>"
                 alt="Current GCash QR Code"
                 style="max-width:220px;width:100%;border:1.5px solid #e5e8e5;border-radius:10px;padding:0.6rem;background:#fff;">
            <p style="font-size:0.78rem;color:#888;margin-top:0.4rem;">Current QR code</p>
        </div>
        <?php else: ?>
        <div style="text-align:center;margin-bottom:1.25rem;padding:1.5rem;background:#f8f9fa;border-radius:10px;border:1.5px dashed #e5e8e5;">
            <p style="color:#aaa;font-size:0.85rem;">No QR code uploaded yet.<br>Guests will only see the account name until you upload one.</p>
        </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <input type="hidden" name="form" value="gcash">
            <div class="form-group">
                <label>GCash Account Name</label>
                <input type="text" name="gcash_account_name" placeholder="e.g. S-Five Inland Resort"
                       value="<?= htmlspecialchars($gcash_settings['account_name']) ?>" required>
            </div>
            <div class="form-group">
                <label>QR Code Image (JPEG only)</label>
                <input type="file" name="gcash_qr" accept="image/jpeg,.jpg,.jpeg">
                <small style="display:block;color:#888;font-size:0.78rem;margin-top:0.3rem;">Upload a photo/screenshot of your GCash QR. JPEG only, max 5MB. Leave blank to keep the current one.</small>
            </div>
            <button type="submit" class="btn-save">Save GCash Details</button>
        </form>
    </div>
</div>

=======
>>>>>>> 9208a6228cdd386865ccdd24f2211d2488455545
<?php include 'partials/footer.php'; ?>
