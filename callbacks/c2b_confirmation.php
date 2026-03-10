<?php
/**
 * C2B Confirmation Callback
 * 
 * This endpoint receives confirmed C2B payments
 * Save the transaction details here
 */

$callbackData = file_get_contents('php://input');

// Log the callback
$logDir = __DIR__ . '/../logs';
if (!file_exists($logDir)) {
    mkdir($logDir, 0777, true);
}

$logFile = $logDir . '/c2b_confirmation_' . date('Y-m-d') . '.log';
file_put_contents($logFile, date('Y-m-d H:i:s') . "\n" . $callbackData . "\n\n", FILE_APPEND);

$data = json_decode($callbackData, true);

header('Content-Type: application/json');

// Extract transaction details
$transID = $data['TransID'] ?? '';
$transAmount = $data['TransAmount'] ?? 0;
$billRefNumber = $data['BillRefNumber'] ?? '';
$msisdn = $data['MSISDN'] ?? '';
$firstName = $data['FirstName'] ?? '';
$middleName = $data['MiddleName'] ?? '';
$lastName = $data['LastName'] ?? '';
$transTime = $data['TransTime'] ?? '';

// TODO: Save to database
// saveC2BTransaction([
//     'trans_id' => $transID,
//     'amount' => $transAmount,
//     'bill_ref' => $billRefNumber,
//     'phone' => $msisdn,
//     'customer_name' => trim("$firstName $middleName $lastName"),
//     'trans_time' => $transTime
// ]);

// Log success
file_put_contents(
    $logFile, 
    "CONFIRMED: TransID $transID - Amount: $transAmount - Bill Ref: $billRefNumber\n\n", 
    FILE_APPEND
);

// Acknowledge receipt
echo json_encode([
    'ResultCode' => 0,
    'ResultDesc' => 'Success'
]);
