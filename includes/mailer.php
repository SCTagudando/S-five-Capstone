<?php

function sendBrevoEmail($toEmail, $toName, $subject, $htmlContent) {
    if (!defined('BREVO_API_KEY') || !BREVO_API_KEY) {
        error_log('sendBrevoEmail: BREVO_API_KEY not set, skipping email to ' . $toEmail);
        return false;
    }

    $payload = [
        'sender'      => [
            'name'  => defined('BREVO_SENDER_NAME') ? BREVO_SENDER_NAME : SITE_NAME,
            'email' => BREVO_SENDER_EMAIL,
        ],
        'to'          => [[ 'email' => $toEmail, 'name' => $toName ]],
        'subject'     => $subject,
        'htmlContent' => $htmlContent,
    ];

    $ch = curl_init('https://api.brevo.com/v3/smtp/email');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_HTTPHEADER     => [
            'accept: application/json',
            'api-key: ' . BREVO_API_KEY,
            'content-type: application/json',
        ],
        CURLOPT_TIMEOUT        => 15,
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    $logDir = __DIR__ . '/../logs/';
    file_put_contents($logDir . 'mailer.log',
        date('Y-m-d H:i:s') . " | to={$toEmail} | subject=\"{$subject}\" | http={$httpCode} | curlErr={$curlErr} | resp={$response}\n",
        FILE_APPEND
    );

    return $httpCode >= 200 && $httpCode < 300;
}

// Sends the "booking confirmed" email once a manual GCash payment is verified
function sendBookingConfirmedEmail($reservation) {
    $checkIn  = date('F j, Y', strtotime($reservation['check_in']));
    $checkOut = date('F j, Y', strtotime($reservation['check_out']));
    $receiptUrl = rtrim(SITE_URL, '/') . '/receipt.php?code=' . urlencode($reservation['booking_code']);

    $subject = 'Booking Confirmed — ' . $reservation['booking_code'] . ' | ' . SITE_NAME;

    $html = '
    <div style="font-family:Arial,Helvetica,sans-serif;max-width:560px;margin:0 auto;color:#1c1c1c;">
        <h2 style="color:#1a3a2e;margin-bottom:0.25rem;">Booking Confirmed ✅</h2>
        <p>Hi ' . htmlspecialchars($reservation['guest_name']) . ',</p>
        <p>Your GCash payment has been verified and your reservation at <strong>' . htmlspecialchars(SITE_NAME) . '</strong> is now <strong>confirmed</strong>.</p>
        <table style="width:100%;border-collapse:collapse;margin:1rem 0;">
            <tr><td style="padding:6px 0;color:#666;">Booking Code</td><td style="padding:6px 0;text-align:right;"><strong>' . htmlspecialchars($reservation['booking_code']) . '</strong></td></tr>
            <tr><td style="padding:6px 0;color:#666;">Cottage</td><td style="padding:6px 0;text-align:right;">' . htmlspecialchars($reservation['cottage_name'] ?? '') . '</td></tr>
            <tr><td style="padding:6px 0;color:#666;">Check-in</td><td style="padding:6px 0;text-align:right;">' . $checkIn . '</td></tr>
            <tr><td style="padding:6px 0;color:#666;">Check-out</td><td style="padding:6px 0;text-align:right;">' . $checkOut . '</td></tr>
            <tr><td style="padding:6px 0;color:#666;">Total Paid</td><td style="padding:6px 0;text-align:right;">₱' . number_format($reservation['total_price'], 2) . '</td></tr>
        </table>
        <p><a href="' . $receiptUrl . '" style="display:inline-block;background:#1a3a2e;color:#fff;padding:0.65rem 1.2rem;text-decoration:none;border-radius:4px;">View / Save Receipt</a></p>
        <p style="font-size:0.85rem;color:#888;margin-top:1.5rem;">See you soon!<br>' . htmlspecialchars(SITE_NAME) . '</p>
    </div>';

    return sendBrevoEmail($reservation['guest_email'], $reservation['guest_name'], $subject, $html);
}
?>
