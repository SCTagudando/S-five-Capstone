<?php
define('PAYMONGO_API_BASE', 'https://api.paymongo.com/v1');

function createPaymongoGcashLink(array $params): array {
    $payload = [
        'data' => [
            'attributes' => [
                'send_email_receipt'   => true,
                'show_description'     => true,
                'show_line_items'      => true,
                'description'          => 'S-Five Resort — ' . $params['description'],
                'reference_number'     => $params['booking_code'],
                'payment_method_types' => ['gcash', 'qrph', 'card', 'paymaya'],
                'line_items' => [
                    [
                        'currency'  => 'PHP',
                        'amount'    => (int)$params['amount'], // centavos
                        'name'      => $params['description'],
                        'quantity'  => 1,
                    ]
                ],
                'billing' => [
                    'name'  => $params['customer_name'] ?? '',
                    'email' => $params['email'] ?? '',
                    'phone' => $params['phone'] ?? '',
                ],
                'metadata' => [
                    'booking_code' => $params['booking_code'],
                ],
            ]
        ]
    ];

    $response = paymongoRequest('POST', '/checkout_sessions', $payload);

    if (!$response || isset($response['errors'])) {
        $errMsg = $response['errors'][0]['detail'] ?? 'PayMongo API error';
        return ['success' => false, 'error' => $errMsg];
    }

    $attr = $response['data']['attributes'] ?? [];
    return [
        'success'          => true,
        'link_id'          => $response['data']['id'], // cs_... checkout session id
        'checkout_url'     => $attr['checkout_url'] ?? '',
        'status'           => $attr['status'] ?? '',
        'reference_number' => $attr['reference_number'] ?? '',
    ];
}

/**
 * Retrieve a checkout session's payment status from PayMongo.
 * $link_id is the cs_... id saved in reservations.paymongo_link_id.
 */
function getPaymongoLinkStatus(string $link_id): array {
    $response = paymongoRequest('GET', '/checkout_sessions/' . $link_id, []);

    if (!$response || isset($response['errors'])) {
        return ['success' => false, 'status' => 'unknown'];
    }

    $attr     = $response['data']['attributes'] ?? [];
    $payments = $attr['payments'] ?? [];
    $paid     = false;
    $payment_id = '';
    $amount     = 0;

    foreach ($payments as $p) {
        if (($p['attributes']['status'] ?? '') === 'paid') {
            $paid       = true;
            $payment_id = $p['id'];
            $amount     = $p['attributes']['amount'] ?? 0;
            break;
        }
    }

    return [
        'success'    => true,
        'status'     => $attr['status'] ?? 'unpaid', // 'active' or 'expired' for checkout sessions
        'paid'       => $paid,
        'payment_id' => $payment_id,
        'amount'     => $amount / 100,
    ];
}

/**
 * Verify PayMongo webhook signature
 */
function verifyPaymongoWebhook(string $rawBody, string $signatureHeader): bool {
    $parts = [];
    foreach (explode(',', $signatureHeader) as $part) {
        [$k, $v] = explode('=', $part, 2);
        $parts[$k] = $v;
    }

    $timestamp = $parts['t']  ?? '';
    $testSig   = $parts['te'] ?? '';
    $liveSig   = $parts['li'] ?? '';

    $toSign   = $timestamp . '.' . $rawBody;
    $secret   = PAYMONGO_WEBHOOK_SECRET;
    $computed = hash_hmac('sha256', $toSign, $secret);

    return hash_equals($computed, $testSig) || hash_equals($computed, $liveSig);
}

/**
 * Core HTTP request to PayMongo API
 */
function paymongoRequest(string $method, string $endpoint, array $data): ?array {
    $credentials = base64_encode(PAYMONGO_SECRET_KEY . ':');
    $url = PAYMONGO_API_BASE . $endpoint;

    $opts = [
        'http' => [
            'method'        => $method,
            'header'        => implode("\r\n", [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Basic ' . $credentials,
            ]),
            'ignore_errors' => true,
        ]
    ];

    if ($method !== 'GET' && !empty($data)) {
        $opts['http']['content'] = json_encode($data);
    }

    $ctx = stream_context_create($opts);
    $raw = @file_get_contents($url, false, $ctx);

    if ($raw === false) return null;
    return json_decode($raw, true);
}
?>