# 🚀 M-Pesa Daraja API Learning Platform

A comprehensive learning project to help you master the M-Pesa Daraja API integration. This project includes working examples, detailed documentation, and an interactive testing interface.

## 📋 Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Quick Start](#quick-start)
- [API Features](#api-features)
- [Testing](#testing)
- [Project Structure](#project-structure)
- [Common Issues](#common-issues)
- [Resources](#resources)

## ✨ Features

- **STK Push (Lipa Na M-Pesa Online)** - Initiate payments from customer phones
- **STK Push Query** - Check transaction status
- **C2B (Customer to Business)** - Receive payments to PayBill/Till
- **B2C (Business to Customer)** - Send money to customers
- **Transaction Status Query** - Check any transaction status
- **Account Balance** - Check M-Pesa account balance
- **Automatic Logging** - All requests and responses logged
- **Interactive Dashboard** - Test all features from web interface
- **Phone Number Formatting** - Automatic format conversion
- **Comprehensive Examples** - Learn from working code

## 📦 Requirements

- PHP 7.4 or higher
- cURL extension enabled
- XAMPP/WAMP/LAMP or any PHP server
- M-Pesa Daraja API credentials (free from Safaricom)
- ngrok (for local callback testing)

## 🔧 Installation

### Step 1: Clone or Download

Place this project in your XAMPP htdocs folder:
```
C:\xampp\htdocs\mpesa\
```

### Step 2: Get Daraja Credentials

1. Visit [Daraja Portal](https://developer.safaricom.co.ke/)
2. Create an account and log in
3. Create a new app (select "Lipa Na M-Pesa Online" or "M-Pesa Express")
4. Copy your **Consumer Key** and **Consumer Secret**

### Step 3: Configure

Edit `config.php` and update your credentials:

```php
'sandbox' => [
    'consumer_key' => 'YOUR_CONSUMER_KEY_HERE',
    'consumer_secret' => 'YOUR_CONSUMER_SECRET_HERE',
    // ... other settings
],
```

### Step 4: Setup Callbacks (for local testing)

1. Download [ngrok](https://ngrok.com/)
2. Run: `ngrok http 80`
3. Copy the https URL (e.g., `https://abc123.ngrok.io`)
4. Update callback URLs in `config.php`:

```php
'callback_urls' => [
    'stk_callback' => 'https://abc123.ngrok.io/mpesa/callbacks/stk_callback.php',
    // ... other callbacks
],
```

### Step 5: Create Logs Directory

The logs directory will be created automatically, but you can create it manually:
```
mkdir logs
```

## ⚙️ Configuration

### Environment Setup

The project works in two modes:

**Sandbox Mode** (for testing):
```php
'environment' => 'sandbox',
```

**Live Mode** (for production):
```php
'environment' => 'live',
```

### Callback URLs

For **local development**, use ngrok:
```
https://your-ngrok-url.ngrok.io/mpesa/callbacks/stk_callback.php
```

For **production**, use your domain:
```
https://yourdomain.com/mpesa/callbacks/stk_callback.php
```

## 🚀 Quick Start

### 1. Test Your Setup

Open your browser and navigate to:
```
http://localhost/mpesa/
```

Click "Generate Token" to verify your credentials are working.

### 2. STK Push Example

```php
<?php
require_once 'includes/MpesaAPI.php';

$mpesa = new MpesaAPI('config.php');

// Format phone number
$phone = MpesaAPI::formatPhoneNumber('0712345678');

// Initiate payment
$result = $mpesa->stkPush(
    $phone,              // Phone number
    100,                 // Amount
    'Invoice123',        // Account reference
    'Payment for goods'  // Description
);

if ($result['status'] == 200 && $result['response']['ResponseCode'] == '0') {
    echo "Success! CheckoutRequestID: " . $result['response']['CheckoutRequestID'];
} else {
    echo "Error: " . json_encode($result['response']);
}
?>
```

### 3. Check Transaction Status

```php
<?php
$checkoutRequestID = 'ws_CO_191220191020363925';
$result = $mpesa->stkQuery($checkoutRequestID);

print_r($result);
?>
```

## 🎯 API Features

### 1. STK Push (Lipa Na M-Pesa Online)

Prompts customer to enter M-Pesa PIN on their phone:

```php
$result = $mpesa->stkPush(
    '254712345678',      // Phone number
    1000,                // Amount (KES)
    'ORDER001',          // Account reference
    'Payment for order'  // Description
);
```

**Response Codes:**
- `0` - Success
- `1032` - Cancelled by user
- `1037` - Timeout
- `1` - Insufficient balance

### 2. C2B (Customer to Business)

Customers pay to your PayBill/Till number:

```php
// Step 1: Register URLs (do this once)
$mpesa->c2bRegisterURLs();

// Step 2: Customers pay via M-Pesa menu
// Your confirmation callback receives the payment

// Step 3 (Sandbox only): Simulate payment
$mpesa->c2bSimulate('254712345678', 1000, 'ACCOUNT123');
```

### 3. B2C (Business to Customer)

Send money to customers:

```php
$result = $mpesa->b2cPayment(
    '254712345678',     // Recipient phone
    1000,               // Amount
    'Refund',           // Occasion
    'Refund for order'  // Remarks
);
```

### 4. Transaction Status

Check any transaction:

```php
$result = $mpesa->transactionStatus('LGR019G3J2');
```

### 5. Account Balance

Check your M-Pesa account balance:

```php
$result = $mpesa->accountBalance();
```

## 🧪 Testing

### Sandbox Test Credentials

**Test Phone Numbers:**
- 254708374149
- 254712345678
- 254711223344

**Test Amounts:** 1 - 70,000

**Default Paybill:** 174379

**Default Passkey:** `bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919`

### Using the Dashboard

1. Open `http://localhost/mpesa/`
2. Test each feature using the interactive forms
3. Check the results displayed on screen
4. Review logs in the `logs/` directory

### Viewing Logs

All API requests and callbacks are logged:

```
logs/
├── mpesa_2026-01-18.log          # API requests/responses
├── stk_callbacks_2026-01-18.log  # STK Push callbacks
├── c2b_validation_2026-01-18.log # C2B validation
└── c2b_confirmation_2026-01-18.log # C2B confirmations
```

## 📁 Project Structure

```
mpesa/
├── api/                          # API endpoints
│   ├── stk_push.php             # STK Push endpoint
│   ├── stk_query.php            # Query endpoint
│   ├── c2b_register.php         # Register C2B URLs
│   ├── c2b_simulate.php         # Simulate C2B
│   └── generate_token.php       # Token generation
│
├── callbacks/                    # Callback handlers
│   ├── stk_callback.php         # STK Push callback
│   ├── c2b_validation.php       # C2B validation
│   ├── c2b_confirmation.php     # C2B confirmation
│   ├── b2c_result.php           # B2C result
│   └── b2c_timeout.php          # B2C timeout
│
├── examples/                     # Code examples
│   ├── stk_push.php             # STK Push example
│   └── c2b_example.php          # C2B example
│
├── includes/                     # Core classes
│   └── MpesaAPI.php             # Main API class
│
├── logs/                         # Transaction logs
│   └── (auto-generated)
│
├── config.php                    # Configuration
├── index.php                     # Dashboard
└── README.md                     # This file
```

## 🔍 Common Issues

### 1. "Failed to generate access token"

**Problem:** Invalid Consumer Key/Secret

**Solution:**
- Verify credentials in config.php
- Ensure you're using sandbox credentials for sandbox environment
- Check that your Daraja app is active

### 2. "The service request is processed successfully" but no callback

**Problem:** Callback URL not accessible

**Solution:**
- Ensure ngrok is running: `ngrok http 80`
- Update callback URLs in config.php with ngrok URL
- Check that callback files exist and are accessible
- Verify callback URLs in Daraja portal match your config

### 3. "Bad Request - Invalid ShortCode"

**Problem:** Wrong shortcode for environment

**Solution:**
- Sandbox: Use 174379
- Live: Use your actual PayBill/Till number

### 4. Phone number format errors

**Problem:** Wrong phone format

**Solution:** Use the formatter:
```php
$phone = MpesaAPI::formatPhoneNumber('0712345678');
// Converts to: 254712345678
```

### 5. Callbacks not being received

**Checklist:**
- ✅ ngrok is running
- ✅ Callback URLs updated in config.php
- ✅ Callback files have correct permissions
- ✅ PHP errors are logged (check error_log)
- ✅ Firewall not blocking incoming requests

## 📚 Resources

### Official Documentation
- [Daraja Portal](https://developer.safaricom.co.ke/)
- [API Documentation](https://developer.safaricom.co.ke/Documentation)
- [Test Credentials](https://developer.safaricom.co.ke/test_credentials)

### Useful Tools
- [ngrok](https://ngrok.com/) - Expose local server
- [Postman](https://www.postman.com/) - API testing
- [JWT.io](https://jwt.io/) - Token debugging

### M-Pesa API Endpoints

**Sandbox:**
```
https://sandbox.safaricom.co.ke
```

**Production:**
```
https://api.safaricom.co.ke
```

## 💡 Tips & Best Practices

### 1. Security
- Never commit credentials to git
- Use environment variables in production
- Validate all callback data
- Implement request signing for callbacks

### 2. Error Handling
- Always check response codes
- Log all transactions
- Implement retry logic for network failures
- Handle timeouts gracefully

### 3. Testing
- Test in sandbox before going live
- Use test phone numbers in sandbox
- Verify all callbacks work correctly
- Test error scenarios

### 4. Production Checklist
- [ ] Updated to live credentials
- [ ] Callback URLs point to production domain
- [ ] SSL certificate installed (HTTPS required)
- [ ] Database setup for transaction storage
- [ ] Error monitoring configured
- [ ] Backup and recovery plan
- [ ] Rate limiting implemented
- [ ] Security audit completed

## 🤝 Support

### Need Help?

1. Check the [Common Issues](#common-issues) section
2. Review the examples in the `examples/` directory
3. Check transaction logs in `logs/` directory
4. Consult the [official documentation](https://developer.safaricom.co.ke/)

### Reporting Issues

When reporting issues, include:
- PHP version
- Error messages from logs
- Request/response from logs
- Steps to reproduce

## 📄 License

This is an educational project. Use it to learn and build your own M-Pesa integrations!

## 🎓 Learning Path

**Beginner:**
1. ✅ Generate access token
2. ✅ Test STK Push
3. ✅ Understand callbacks

**Intermediate:**
4. ✅ Implement C2B
5. ✅ Add database storage
6. ✅ Handle errors properly

**Advanced:**
7. ✅ Implement B2C
8. ✅ Build complete checkout flow
9. ✅ Deploy to production

---

**Happy Coding! 🎉**

Built with ❤️ for learning M-Pesa Daraja API
