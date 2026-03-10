<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation - M-Pesa Integration</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .docs-container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 2rem;
        }
        .docs-nav {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
        }
        .docs-nav ul {
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .docs-nav a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        .docs-section {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
        }
        .docs-section h2 {
            color: var(--dark-color);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-color);
        }
        .docs-section h3 {
            color: var(--dark-color);
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }
        .code-block {
            background: #f8f9fa;
            border-left: 4px solid var(--primary-color);
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 4px;
            overflow-x: auto;
        }
        .code-block code {
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            line-height: 1.5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        th {
            background: var(--light-color);
            font-weight: 600;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>Access Tech Solutions</h1>
                    <span class="subtitle">M-Pesa Integration Platform</span>
                </div>
                <nav class="nav">
                    <a href="index.php" class="nav-link">Home</a>
                    <a href="#" class="nav-link">Documentation</a>
                </nav>
            </div>
        </div>
    </header>

    <div class="docs-container">
        <div class="docs-nav">
            <ul>
                <li><a href="#overview">Overview</a></li>
                <li><a href="#authentication">Authentication</a></li>
                <li><a href="#stk-push">STK Push</a></li>
                <li><a href="#c2b">C2B Payments</a></li>
                <li><a href="#responses">Response Codes</a></li>
                <li><a href="#testing">Testing</a></li>
            </ul>
        </div>

        <div id="overview" class="docs-section">
            <h2>Overview</h2>
            <p>This platform provides seamless integration with M-Pesa's Daraja API, enabling businesses to accept and process mobile money payments.</p>
            
            <h3>Key Features</h3>
            <ul>
                <li>STK Push (Lipa Na M-Pesa Online) - Initiate payment requests</li>
                <li>C2B Payments - Accept payments to PayBill/Till numbers</li>
                <li>Transaction Status Queries - Real-time status checking</li>
                <li>Comprehensive Logging - All requests and responses logged</li>
                <li>Secure Callbacks - Webhook handling for payment notifications</li>
            </ul>
        </div>

        <div id="authentication" class="docs-section">
            <h2>Authentication</h2>
            <p>All API requests require authentication using OAuth 2.0 Bearer tokens.</p>
            
            <h3>Generate Access Token</h3>
            <div class="code-block">
                <code>POST /api/generate_token.php</code>
            </div>
            
            <p><strong>Response:</strong></p>
            <div class="code-block">
                <code>{
    "success": true,
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJ..."
}</code>
            </div>
            <p>Tokens are valid for 1 hour.</p>
        </div>

        <div id="stk-push" class="docs-section">
            <h2>STK Push (Lipa Na M-Pesa Online)</h2>
            <p>STK Push sends a payment prompt directly to the customer's phone.</p>
            
            <h3>Request</h3>
            <div class="code-block">
                <code>POST /api/stk_push.php

Parameters:
- phone: Customer phone number (254XXXXXXXXX)
- amount: Amount to charge (minimum 1)
- reference: Account reference (alphanumeric)
- description: Transaction description</code>
            </div>
            
            <h3>Success Response</h3>
            <div class="code-block">
                <code>{
    "status": 200,
    "response": {
        "MerchantRequestID": "29115-34620561-1",
        "CheckoutRequestID": "ws_CO_191220191020363925",
        "ResponseCode": "0",
        "ResponseDescription": "Success. Request accepted for processing",
        "CustomerMessage": "Success. Request accepted for processing"
    }
}</code>
            </div>
            
            <h3>Customer Experience</h3>
            <ol>
                <li>Customer receives M-Pesa prompt on their phone</li>
                <li>Customer enters M-Pesa PIN</li>
                <li>Payment processed</li>
                <li>Both parties receive confirmation</li>
                <li>Callback sent to your server</li>
            </ol>
        </div>

        <div id="c2b" class="docs-section">
            <h2>C2B (Customer to Business) Payments</h2>
            <p>C2B allows customers to pay directly to your PayBill or Till number.</p>
            
            <h3>Step 1: Register URLs</h3>
            <div class="code-block">
                <code>POST /api/c2b_register.php</code>
            </div>
            <p>Register your validation and confirmation URLs (one-time setup).</p>
            
            <h3>Step 2: Customer Makes Payment</h3>
            <p>Customer uses M-Pesa menu:</p>
            <ol>
                <li>Lipa na M-Pesa</li>
                <li>Pay Bill or Buy Goods</li>
                <li>Enter business number</li>
                <li>Enter account number</li>
                <li>Enter amount</li>
                <li>Confirm with PIN</li>
            </ol>
            
            <h3>Step 3: Receive Callbacks</h3>
            <p>Your confirmation URL receives payment details:</p>
            <div class="code-block">
                <code>{
    "TransactionType": "Pay Bill",
    "TransID": "LGR019G3J2",
    "TransAmount": "100.00",
    "BusinessShortCode": "600126",
    "BillRefNumber": "account123",
    "MSISDN": "254708374149",
    "FirstName": "John"
}</code>
            </div>
        </div>

        <div id="responses" class="docs-section">
            <h2>Response Codes</h2>
            
            <h3>STK Push Response Codes</h3>
            <table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>0</td>
                        <td>Success</td>
                        <td>Transaction completed successfully</td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>Insufficient Balance</td>
                        <td>Customer has insufficient funds</td>
                    </tr>
                    <tr>
                        <td>1032</td>
                        <td>Cancelled by User</td>
                        <td>Customer cancelled the request</td>
                    </tr>
                    <tr>
                        <td>1037</td>
                        <td>Timeout</td>
                        <td>User didn't enter PIN in time</td>
                    </tr>
                    <tr>
                        <td>2001</td>
                        <td>Invalid Initiator</td>
                        <td>Check your credentials</td>
                    </tr>
                </tbody>
            </table>
            
            <h3>HTTP Status Codes</h3>
            <table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Meaning</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>200</td>
                        <td>Success</td>
                    </tr>
                    <tr>
                        <td>400</td>
                        <td>Bad Request</td>
                    </tr>
                    <tr>
                        <td>401</td>
                        <td>Unauthorized</td>
                    </tr>
                    <tr>
                        <td>500</td>
                        <td>Server Error</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="testing" class="docs-section">
            <h2>Testing</h2>
            
            <h3>Sandbox Environment</h3>
            <p>Current configuration uses Safaricom's sandbox for safe testing.</p>
            
            <table>
                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Environment</td>
                        <td>Sandbox</td>
                    </tr>
                    <tr>
                        <td>Test Phone Numbers</td>
                        <td>254708374149, 254712345678</td>
                    </tr>
                    <tr>
                        <td>Test Amounts</td>
                        <td>1 - 70,000 KES</td>
                    </tr>
                    <tr>
                        <td>Shortcode</td>
                        <td>174379</td>
                    </tr>
                </tbody>
            </table>
            
            <h3>Best Practices</h3>
            <ul>
                <li>Always validate phone numbers before sending requests</li>
                <li>Implement proper error handling</li>
                <li>Log all transactions for debugging and reconciliation</li>
                <li>Use idempotency keys to prevent duplicate charges</li>
                <li>Test thoroughly in sandbox before going live</li>
                <li>Monitor callbacks and implement retry logic</li>
            </ul>
        </div>

        <div class="docs-section">
            <h2>Need Help?</h2>
            <p>For assistance with integration or technical support:</p>
            <ul>
                <li>Email: info@accesstech.co.ke</li>
                <li>Website: www.accesstech.co.ke</li>
                <li>Daraja Portal: <a href="https://developer.safaricom.co.ke" target="_blank">developer.safaricom.co.ke</a></li>
            </ul>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2026 Access Tech Solutions LTD. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
