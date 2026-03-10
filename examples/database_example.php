<?php
/**
 * Database Integration Example
 * 
 * This example shows how to store M-Pesa transactions in a database
 */

require_once '../includes/MpesaAPI.php';
require_once '../includes/Database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Integration - M-Pesa Daraja API</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 { color: #333; }
        .code-block {
            background: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #17a2b8;
            margin: 20px 0;
            overflow-x: auto;
        }
        pre { margin: 0; }
        .info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>💾 Database Integration Example</h1>
        
        <div class="info">
            <strong>Prerequisites:</strong> Before using this example, create the database using <code>database.sql</code> file.
            Run it in phpMyAdmin or MySQL command line.
        </div>
        
        <h2>Step 1: Create Database</h2>
        <p>Import the <code>database.sql</code> file in phpMyAdmin or run:</p>
        <div class="code-block">
            <pre><code>mysql -u root -p < database.sql</code></pre>
        </div>
        
        <h2>Step 2: Update Configuration</h2>
        <p>Ensure your database credentials are correct in <code>config.php</code>:</p>
        <div class="code-block">
            <pre><code>'database' => [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'mpesa_learning',
],</code></pre>
        </div>
        
        <h2>Step 3: STK Push with Database Storage</h2>
        <div class="code-block">
            <pre><code>&lt;?php
require_once 'includes/MpesaAPI.php';
require_once 'includes/Database.php';

// Initialize
$mpesa = new MpesaAPI('config.php');
$db = new Database('config.php');

// Format phone
$phone = MpesaAPI::formatPhoneNumber('0712345678');

// Initiate STK Push
$result = $mpesa->stkPush($phone, 1000, 'INV001', 'Payment');

if ($result['status'] == 200 && $result['response']['ResponseCode'] == '0') {
    // Save to database
    $db->saveSTKRequest([
        'merchant_request_id' => $result['response']['MerchantRequestID'],
        'checkout_request_id' => $result['response']['CheckoutRequestID'],
        'phone_number' => $phone,
        'amount' => 1000,
        'account_reference' => 'INV001',
        'transaction_desc' => 'Payment'
    ]);
    
    echo "Transaction initiated and saved to database!";
}
?&gt;</code></pre>
        </div>
        
        <h2>Step 4: Update Callback Handler</h2>
        <p>Modify <code>callbacks/stk_callback.php</code> to save results:</p>
        <div class="code-block">
            <pre><code>&lt;?php
require_once '../includes/Database.php';

$callbackData = file_get_contents('php://input');
$data = json_decode($callbackData, true);

$resultCode = $data['Body']['stkCallback']['ResultCode'] ?? null;
$checkoutRequestID = $data['Body']['stkCallback']['CheckoutRequestID'] ?? null;

if ($resultCode == 0) {
    // Success - extract details
    $callbackMetadata = $data['Body']['stkCallback']['CallbackMetadata']['Item'];
    
    $transactionData = [];
    foreach ($callbackMetadata as $item) {
        $transactionData[$item['Name']] = $item['Value'] ?? null;
    }
    
    // Save to database
    $db = new Database('../config.php');
    $db->updateSTKCallback([
        'checkout_request_id' => $checkoutRequestID,
        'mpesa_receipt_number' => $transactionData['MpesaReceiptNumber'],
        'transaction_date' => $transactionData['TransactionDate'],
        'result_code' => $resultCode,
        'result_desc' => 'Success'
    ]);
} else {
    // Failed - update status
    $db = new Database('../config.php');
    $db->updateSTKCallback([
        'checkout_request_id' => $checkoutRequestID,
        'mpesa_receipt_number' => null,
        'transaction_date' => null,
        'result_code' => $resultCode,
        'result_desc' => $data['Body']['stkCallback']['ResultDesc']
    ]);
}

// Acknowledge
echo json_encode(['ResultCode' => 0, 'ResultDesc' => 'Success']);
?&gt;</code></pre>
        </div>
        
        <h2>Step 5: Query Transactions</h2>
        <div class="code-block">
            <pre><code>&lt;?php
require_once 'includes/Database.php';

$db = new Database('config.php');

// Get transaction by checkout request ID
$transaction = $db->getSTKTransaction('ws_CO_191220191020363925');
print_r($transaction);

// Get customer's transactions
$transactions = $db->getTransactionsByPhone('254712345678', 10);
foreach ($transactions as $trans) {
    echo "Amount: {$trans['amount']}, Status: {$trans['status']}\n";
}

// Get today's stats
$stats = $db->getTodayStats();
echo "Total Today: {$stats['total_amount']} KES\n";
echo "Successful: {$stats['successful']}\n";
echo "Failed: {$stats['failed']}\n";
?&gt;</code></pre>
        </div>
        
        <h2>Useful Database Queries</h2>
        
        <h3>Get all completed transactions:</h3>
        <div class="code-block">
            <pre><code>SELECT * FROM stk_transactions 
WHERE status = 'completed' 
ORDER BY created_at DESC;</code></pre>
        </div>
        
        <h3>Get total collected today:</h3>
        <div class="code-block">
            <pre><code>SELECT SUM(amount) as total_today 
FROM stk_transactions 
WHERE status = 'completed' 
AND DATE(created_at) = CURDATE();</code></pre>
        </div>
        
        <h3>Get transactions by account reference:</h3>
        <div class="code-block">
            <pre><code>SELECT * FROM stk_transactions 
WHERE account_reference = 'INV001';</code></pre>
        </div>
        
        <h3>Daily statistics:</h3>
        <div class="code-block">
            <pre><code>SELECT 
    DATE(created_at) as date,
    COUNT(*) as total,
    SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as revenue
FROM stk_transactions
GROUP BY DATE(created_at)
ORDER BY date DESC;</code></pre>
        </div>
        
        <h2>Benefits of Database Storage</h2>
        <ul>
            <li>✅ Persistent transaction records</li>
            <li>✅ Easy reporting and analytics</li>
            <li>✅ Customer transaction history</li>
            <li>✅ Audit trail for compliance</li>
            <li>✅ Reconciliation with M-Pesa</li>
            <li>✅ Real-time dashboards</li>
        </ul>
        
        <a href="../index.php" class="back-link">← Back to Dashboard</a>
    </div>
</body>
</html>
