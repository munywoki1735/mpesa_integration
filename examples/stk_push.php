<?php
/**
 * STK Push Example - Direct PHP Implementation
 * 
 * This example shows how to use the MpesaAPI class directly in PHP
 */

require_once '../includes/MpesaAPI.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STK Push Example - M-Pesa Daraja API</title>
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
            border-left: 4px solid #007bff;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>📱 STK Push Example</h1>
        <p>This example demonstrates how to initiate an STK Push request using PHP.</p>
        
        <h2>Basic Usage:</h2>
        <div class="code-block">
            <pre><code>&lt;?php
require_once 'includes/MpesaAPI.php';

// Initialize the API
$mpesa = new MpesaAPI('config.php');

// Format phone number (converts 0712345678 to 254712345678)
$phone = MpesaAPI::formatPhoneNumber('0712345678');

// Initiate STK Push
$result = $mpesa->stkPush(
    $phone,              // Phone number
    100,                 // Amount
    'Invoice123',        // Account reference
    'Payment for goods'  // Description
);

// Check result
if ($result['status'] == 200 && $result['response']['ResponseCode'] == '0') {
    echo "Success! CheckoutRequestID: " . $result['response']['CheckoutRequestID'];
} else {
    echo "Error: " . $result['response']['errorMessage'];
}
?&gt;</code></pre>
        </div>
        
        <h2>Response Structure:</h2>
        <div class="code-block">
            <pre><code>// Successful Response:
{
    "status": 200,
    "response": {
        "MerchantRequestID": "29115-34620561-1",
        "CheckoutRequestID": "ws_CO_191220191020363925",
        "ResponseCode": "0",
        "ResponseDescription": "Success. Request accepted for processing",
        "CustomerMessage": "Success. Request accepted for processing"
    }
}

// Error Response:
{
    "status": 400,
    "response": {
        "requestId": "11728-2929992-1",
        "errorCode": "400.002.02",
        "errorMessage": "Bad Request - Invalid Amount"
    }
}</code></pre>
        </div>
        
        <h2>Testing in Sandbox:</h2>
        <ul>
            <li>Use test phone numbers: 254708374149, 254712345678</li>
            <li>Test amounts: 1 - 70,000</li>
            <li>Callbacks will be sent to your registered URL</li>
            <li>Check logs folder for request/response details</li>
        </ul>
        
        <h2>Common Response Codes:</h2>
        <ul>
            <li><strong>0:</strong> Success - Request accepted</li>
            <li><strong>1:</strong> Insufficient balance</li>
            <li><strong>1032:</strong> Request cancelled by user</li>
            <li><strong>1037:</strong> Timeout - User didn't enter PIN in time</li>
            <li><strong>2001:</strong> Invalid initiator information</li>
        </ul>
        
        <a href="../index.php" class="back-link">← Back to Dashboard</a>
    </div>
</body>
</html>
