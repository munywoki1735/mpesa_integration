<?php
/**
 * C2B (Customer to Business) Example
 * 
 * This example shows how to implement C2B payments
 */

require_once '../includes/MpesaAPI.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>C2B Example - M-Pesa Daraja API</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
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
        h1 {
            color: #333;
        }
        .code-block {
            background: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #28a745;
            margin: 20px 0;
            overflow-x: auto;
        }
        pre {
            margin: 0;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
        }
        .step {
            background: #e7f3ff;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📥 C2B (Customer to Business) Example</h1>
        <p>C2B allows customers to pay to your PayBill or Till Number directly.</p>
        
        <h2>Step 1: Register URLs</h2>
        <div class="step">
            <p>First, register your validation and confirmation URLs:</p>
        </div>
        <div class="code-block">
            <pre><code>&lt;?php
require_once 'includes/MpesaAPI.php';

$mpesa = new MpesaAPI('config.php');

// Register URLs
$result = $mpesa->c2bRegisterURLs();

if ($result['status'] == 200 && $result['response']['ResponseCode'] == '0') {
    echo "URLs registered successfully!";
}
?&gt;</code></pre>
        </div>
        
        <h2>Step 2: Implement Validation Callback</h2>
        <div class="step">
            <p>Create a validation endpoint (callbacks/c2b_validation.php):</p>
        </div>
        <div class="code-block">
            <pre><code>&lt;?php
$data = json_decode(file_get_contents('php://input'), true);

// Validate the payment
$transAmount = $data['TransAmount'] ?? 0;
$billRefNumber = $data['BillRefNumber'] ?? '';

// Your validation logic here
if ($transAmount < 10) {
    echo json_encode([
        'ResultCode' => 1,
        'ResultDesc' => 'Amount too small'
    ]);
} else {
    echo json_encode([
        'ResultCode' => 0,
        'ResultDesc' => 'Accepted'
    ]);
}
?&gt;</code></pre>
        </div>
        
        <h2>Step 3: Implement Confirmation Callback</h2>
        <div class="step">
            <p>Create a confirmation endpoint (callbacks/c2b_confirmation.php):</p>
        </div>
        <div class="code-block">
            <pre><code>&lt;?php
$data = json_decode(file_get_contents('php://input'), true);

// Save the transaction
$transID = $data['TransID'];
$transAmount = $data['TransAmount'];
$billRefNumber = $data['BillRefNumber'];
$msisdn = $data['MSISDN'];

// Save to database
saveTransaction([
    'trans_id' => $transID,
    'amount' => $transAmount,
    'account' => $billRefNumber,
    'phone' => $msisdn
]);

// Acknowledge
echo json_encode([
    'ResultCode' => 0,
    'ResultDesc' => 'Success'
]);
?&gt;</code></pre>
        </div>
        
        <h2>Step 4: Test with Simulation (Sandbox Only)</h2>
        <div class="step">
            <p>In sandbox, you can simulate C2B payments:</p>
        </div>
        <div class="code-block">
            <pre><code>&lt;?php
$mpesa = new MpesaAPI('config.php');

$result = $mpesa->c2bSimulate(
    '254712345678',  // Phone number
    1000,            // Amount
    'Account123'     // Bill reference
);

if ($result['status'] == 200) {
    echo "Payment simulated successfully!";
}
?&gt;</code></pre>
        </div>
        
        <h2>Callback Data Structure:</h2>
        <div class="code-block">
            <pre><code>{
    "TransactionType": "Pay Bill",
    "TransID": "LGR019G3J2",
    "TransTime": "20191122062345",
    "TransAmount": "10.00",
    "BusinessShortCode": "600126",
    "BillRefNumber": "account123",
    "InvoiceNumber": "",
    "OrgAccountBalance": "49197.00",
    "ThirdPartyTransID": "",
    "MSISDN": "254708374149",
    "FirstName": "John",
    "MiddleName": "Doe",
    "LastName": ""
}</code></pre>
        </div>
        
        <h2>Important Notes:</h2>
        <ul>
            <li>URLs must be publicly accessible (use ngrok for local testing)</li>
            <li>Validation is optional - you can skip it by not implementing the endpoint</li>
            <li>Confirmation is where you save the transaction to your database</li>
            <li>Always respond with ResultCode 0 to acknowledge receipt</li>
            <li>In production, customers pay using: Lipa na M-Pesa → PayBill/Till → Enter number → Enter amount</li>
        </ul>
        
        <a href="../index.php" class="back-link">← Back to Dashboard</a>
    </div>
</body>
</html>
