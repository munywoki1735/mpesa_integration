<?php
/**
 * C2B Validation Callback
 * 
 * This endpoint validates incoming C2B payments
 * Return ResultCode 0 to accept, any other code to reject
 */

$callbackData = file_get_contents('php://input');

// Log the callback
$logDir = __DIR__ . '/../logs';
if (!file_exists($logDir)) {
    mkdir($logDir, 0777, true);
}

$logFile = $logDir . '/c2b_validation_' . date('Y-m-d') . '.log';
file_put_contents($logFile, date('Y-m-d H:i:s') . "\n" . $callbackData . "\n\n", FILE_APPEND);

$data = json_decode($callbackData, true);

header('Content-Type: application/json');

// Validate the payment
// Example: Check if account reference exists, amount is correct, etc.

$transID = $data['TransID'] ?? '';
$transAmount = $data['TransAmount'] ?? 0;
$billRefNumber = $data['BillRefNumber'] ?? '';
$msisdn = $data['MSISDN'] ?? '';

// Add your validation logic here
$isValid = true; // Change based on your validation

if ($isValid) {
    echo json_encode([
        'ResultCode' => 0,
        'ResultDesc' => 'Accepted'
    ]);
} else {
    echo json_encode([
        'ResultCode' => 1,
        'ResultDesc' => 'Rejected'
    ]);
}
