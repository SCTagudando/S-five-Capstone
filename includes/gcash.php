<?php
<<<<<<< HEAD
=======

/**
 * Save a pending GCash payment submission after booking.
 * Called right after the reservation is inserted.
 */
>>>>>>> 9208a6228cdd386865ccdd24f2211d2488455545
function saveGcashSubmission(array $params): array {


    $db = $params['db'];

    try {
        $stmt = $db->prepare("
            INSERT INTO gcash_payments
                (reservation_id, reference_number, amount, sender_name, sender_number, proof_image, status)
            VALUES (?, ?, ?, ?, ?, ?, 'Pending')
        ");
        $stmt->execute([
            $params['reservation_id'],
            $params['reference_number'],
            $params['amount'],
            $params['guest_name'],
            $params['guest_phone'],
            $params['proof_image'] ?? '',
        ]);

        return ['success' => true];
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Handle screenshot upload for GCash proof.
 * Returns the saved filename or '' on failure.
 */
function uploadGcashProof(array $file, string $booking_code): string {
    if (empty($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return '';
    }

    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $mime    = mime_content_type($file['tmp_name']);

    if (!in_array($mime, $allowed)) {
        return '';
    }

    if ($file['size'] > 5 * 1024 * 1024) { // 5 MB max
        return '';
    }

    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'gcash_' . preg_replace('/[^a-zA-Z0-9]/', '', $booking_code) . '_' . time() . '.' . strtolower($ext);
    $dest     = __DIR__ . '/../uploads/gcash/' . $filename;

    if (!is_dir(dirname($dest))) {
        mkdir(dirname($dest), 0755, true);
    }

    return move_uploaded_file($file['tmp_name'], $dest) ? $filename : '';
}
<<<<<<< HEAD

/**
 * Get the current GCash QR code + account name shown to guests.
 * Falls back to the legacy GCASH_ACCOUNT_NAME constant if the
 * settings row hasn't been created yet.
 */
function getGcashSettings(PDO $db): array {
    $row = $db->query("SELECT account_name, qr_image FROM gcash_settings WHERE id = 1")->fetch();

    if (!$row) {
        return [
            'account_name' => defined('GCASH_ACCOUNT_NAME') ? GCASH_ACCOUNT_NAME : 'S-Five Inland Resort',
            'qr_image'     => null,
        ];
    }

    return $row;
}

/**
 * Save/replace the admin-uploaded GCash QR code image and account name.
 * QR image is restricted to JPEG only. Returns ['success'=>bool,'error'=>?string].
 */
function saveGcashSettings(PDO $db, string $account_name, ?array $file): array {
    $upload_dir  = __DIR__ . '/../uploads/gcash/';
    $new_filename = null;

    if ($file && !empty($file['name']) && $file['error'] === UPLOAD_ERR_OK) {
        $mime = mime_content_type($file['tmp_name']);
        if ($mime !== 'image/jpeg') {
            return ['success' => false, 'error' => 'QR code image must be a JPEG (.jpg) file.'];
        }
        if ($file['size'] > 5 * 1024 * 1024) { // 5 MB max
            return ['success' => false, 'error' => 'QR code image must be under 5MB.'];
        }

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $new_filename = 'gcash_qr_' . time() . '.jpg';
        if (!move_uploaded_file($file['tmp_name'], $upload_dir . $new_filename)) {
            return ['success' => false, 'error' => 'Failed to upload QR code image.'];
        }
    }

    $current = $db->query("SELECT qr_image FROM gcash_settings WHERE id = 1")->fetch();

    if ($current) {
        if ($new_filename) {
            $db->prepare("UPDATE gcash_settings SET account_name = ?, qr_image = ? WHERE id = 1")
               ->execute([$account_name, $new_filename]);
            // Remove the old QR file now that the new one is saved.
            if (!empty($current['qr_image']) && file_exists($upload_dir . $current['qr_image'])) {
                @unlink($upload_dir . $current['qr_image']);
            }
        } else {
            $db->prepare("UPDATE gcash_settings SET account_name = ? WHERE id = 1")
               ->execute([$account_name]);
        }
    } else {
        $db->prepare("INSERT INTO gcash_settings (id, account_name, qr_image) VALUES (1, ?, ?)")
           ->execute([$account_name, $new_filename]);
    }

    return ['success' => true];
}
=======
>>>>>>> 9208a6228cdd386865ccdd24f2211d2488455545
?>