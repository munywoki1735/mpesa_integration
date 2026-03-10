<?php
/**
 * STK Push Callback Handler
 * 
 * This file receives the callback from M-Pesa after STK Push
 * M-Pesa will POST the transaction result here
 */

// Get the callback data
$callbackData = file_get_contents('php://input');

// Log the callback
$logDir = __DIR__ . '/../logs';
if (!file_exists($logDir)) {
    mkdir($logDir, 0777, true);
}

$logFile = $logDir . '/stk_callbacks_' . date('Y-m-d') . '.log';
file_put_contents($logFile, date('Y-m-d H:i:s') . "\n" . $callbackData . "\n\n", FILE_APPEND);

// Decode the callback
$data = json_decode($callbackData, true);

// Response to M-Pesa
header('Content-Type: application/json');

if (!$data) {
    echo json_encode(['ResultCode' => 1, 'ResultDesc' => 'Invalid data']);
    exit;
}

// Extract important information
$resultCode = $data['Body']['stkCallback']['ResultCode'] ?? null;
$resultDesc = $data['Body']['stkCallback']['ResultDesc'] ?? null;
$merchantRequestID = $data['Body']['stkCallback']['MerchantRequestID'] ?? null;
$checkoutRequestID = $data['Body']['stkCallback']['CheckoutRequestID'] ?? null;

if ($resultCode == 0) {
    // Transaction successful
    $callbackMetadata = $data['Body']['stkCallback']['CallbackMetadata']['Item'] ?? [];
    
    $transactionData = [];
    foreach ($callbackMetadata as $item) {
        $transactionData[$item['Name']] = $item['Value'] ?? null;
    }
    
    // Extract transaction details
    $amount = $transactionData['Amount'] ?? 0;
    $mpesaReceiptNumber = $transactionData['MpesaReceiptNumber'] ?? '';
    $transactionDate = $transactionData['TransactionDate'] ?? '';
    $phoneNumber = $transactionData['PhoneNumber'] ?? '';
    
    // TODO: Save to database
    // Example:
    // saveTransaction([
    //     'merchant_request_id' => $merchantRequestID,
    //     'checkout_request_id' => $checkoutRequestID,
    //     'amount' => $amount,
    //     'mpesa_receipt' => $mpesaReceiptNumber,
    //     'phone' => $phoneNumber,
    //     'transaction_date' => $transactionDate,
    //     'status' => 'completed'
    // ]);
    
    // Log success
    file_put_contents(
        $logFile, 
        "SUCCESS: Receipt $mpesaReceiptNumber - Amount: $amount - Phone: $phoneNumber\n\n", 
        FILE_APPEND
    );
    
} else {
    // Transaction failed
    file_put_contents(
        $logFile, 
        "FAILED: $resultDesc (Code: $resultCode)\n\n", 
        FILE_APPEND
    );
    
    // TODO: Update database with failed status
}

// Respond to M-Pesa
echo json_encode([
    'ResultCode' => 0,
    'ResultDesc' => 'Success'
]);
