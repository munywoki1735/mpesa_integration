<?php
/**
 * B2C Timeout Callback
 * 
 * Called when B2C request times out
 */

$callbackData = file_get_contents('php://input');

// Log the callback
$logDir = __DIR__ . '/../logs';
if (!file_exists($logDir)) {
    mkdir($logDir, 0777, true);
}

$logFile = $logDir . '/b2c_timeout_' . date('Y-m-d') . '.log';
file_put_contents($logFile, date('Y-m-d H:i:s') . "\n" . $callbackData . "\n\n", FILE_APPEND);

header('Content-Type: application/json');
echo json_encode([
    'ResultCode' => 0,
    'ResultDesc' => 'Success'
]);
