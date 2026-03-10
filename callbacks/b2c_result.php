<?php
/**
 * B2C Result Callback
 * 
 * Receives the result of B2C payment requests
 */

$callbackData = file_get_contents('php://input');

// Log the callback
$logDir = __DIR__ . '/../logs';
if (!file_exists($logDir)) {
    mkdir($logDir, 0777, true);
}

$logFile = $logDir . '/b2c_results_' . date('Y-m-d') . '.log';
file_put_contents($logFile, date('Y-m-d H:i:s') . "\n" . $callbackData . "\n\n", FILE_APPEND);

$data = json_decode($callbackData, true);

header('Content-Type: application/json');

$resultCode = $data['Result']['ResultCode'] ?? null;
$resultDesc = $data['Result']['ResultDesc'] ?? null;
$conversationID = $data['Result']['ConversationID'] ?? '';
$originatorConversationID = $data['Result']['OriginatorConversationID'] ?? '';

if ($resultCode == 0) {
    // B2C successful
    $resultParameters = $data['Result']['ResultParameters']['ResultParameter'] ?? [];
    
    $transactionData = [];
    foreach ($resultParameters as $param) {
        $transactionData[$param['Key']] = $param['Value'] ?? null;
    }
    
    file_put_contents(
        $logFile, 
        "SUCCESS: " . json_encode($transactionData, JSON_PRETTY_PRINT) . "\n\n", 
        FILE_APPEND
    );
} else {
    file_put_contents(
        $logFile, 
        "FAILED: $resultDesc (Code: $resultCode)\n\n", 
        FILE_APPEND
    );
}

echo json_encode([
    'ResultCode' => 0,
    'ResultDesc' => 'Success'
]);
