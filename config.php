<?php
/**
 * M-Pesa Daraja API Configuration
 * Access Tech Solutions LTD
 * 
 * SETUP:
 * 1. Rename env.example.txt to .env
 * 2. Update .env with your credentials
 * 3. This file will automatically load from .env if available
 * 
 * SANDBOX TESTING:
 * - STK Push: Use shortcode 174379
 * - C2B Testing: Use PayBill 880100 with Account 5503900011
 * - Test Phone: 254708374149, 254712345678
 */

// Load environment variables if Env class exists
if (file_exists(__DIR__ . '/includes/Env.php')) {
    require_once __DIR__ . '/includes/Env.php';
    try {
        Env::load(__DIR__ . '/.env');
    } catch (Exception $e) {
        // .env file not found, will use hardcoded values below
    }
}

// Helper function to get env variable with fallback
function env($key, $default = null) {
    if (class_exists('Env')) {
        return Env::get($key, $default);
    }
    $value = getenv($key);
    return $value !== false ? $value : $default;
}

return [
    // Environment: 'sandbox' or 'live'
    'environment' => env('MPESA_ENV', 'sandbox'),
    
    // Sandbox Credentials
    'sandbox' => [
        'consumer_key' => env('MPESA_CONSUMER_KEY', 'zr0mS1g7c2u7vE6YZBBdUWiefU4xBHhizye34lbJsQCGfHws'),
        'consumer_secret' => env('MPESA_CONSUMER_SECRET', 'B27BMyC3uUbz9XogHirv5LjAlZ2Yp9ZYTYRnS0usdgdczjAtQ4FnGGHCfdH5gSEY'),
        'passkey' => env('MPESA_PASSKEY', 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919'),
        'shortcode' => env('MPESA_SHORTCODE', '174379'),
        'initiator_name' => env('MPESA_INITIATOR_NAME', 'testapi'),
        'security_credential' => env('MPESA_SECURITY_CREDENTIAL', 'YOUR_SECURITY_CREDENTIAL'),
    ],
    
    // Live/Production Credentials
    'live' => [
        'consumer_key' => env('MPESA_LIVE_CONSUMER_KEY', 'YOUR_LIVE_CONSUMER_KEY'),
        'consumer_secret' => env('MPESA_LIVE_CONSUMER_SECRET', 'YOUR_LIVE_CONSUMER_SECRET'),
        'passkey' => env('MPESA_LIVE_PASSKEY', 'YOUR_LIVE_PASSKEY'),
        'shortcode' => env('MPESA_LIVE_SHORTCODE', 'YOUR_SHORTCODE'),
        'initiator_name' => env('MPESA_LIVE_INITIATOR_NAME', 'YOUR_INITIATOR_NAME'),
        'security_credential' => env('MPESA_LIVE_SECURITY_CREDENTIAL', 'YOUR_SECURITY_CREDENTIAL'),
    ],
    
    // API Endpoints
    'endpoints' => [
        'sandbox' => [
            'base_url' => 'https://sandbox.safaricom.co.ke',
            'oauth' => '/oauth/v1/generate?grant_type=client_credentials',
            'stk_push' => '/mpesa/stkpush/v1/processrequest',
            'stk_query' => '/mpesa/stkpushquery/v1/query',
            'c2b_register' => '/mpesa/c2b/v1/registerurl',
            'c2b_simulate' => '/mpesa/c2b/v1/simulate',
            'b2c' => '/mpesa/b2c/v1/paymentrequest',
            'transaction_status' => '/mpesa/transactionstatus/v1/query',
            'account_balance' => '/mpesa/accountbalance/v1/query',
            'reversal' => '/mpesa/reversal/v1/request',
        ],
        'live' => [
            'base_url' => 'https://api.safaricom.co.ke',
            'oauth' => '/oauth/v1/generate?grant_type=client_credentials',
            'stk_push' => '/mpesa/stkpush/v1/processrequest',
            'stk_query' => '/mpesa/stkpushquery/v1/query',
            'c2b_register' => '/mpesa/c2b/v1/registerurl',
            'c2b_simulate' => '/mpesa/c2b/v1/simulate',
            'b2c' => '/mpesa/b2c/v1/paymentrequest',
            'transaction_status' => '/mpesa/transactionstatus/v1/query',
            'account_balance' => '/mpesa/accountbalance/v1/query',
            'reversal' => '/mpesa/reversal/v1/request',
        ],
    ],
    
    // Callback URLs
    'callback_urls' => [
        'stk_callback' => env('CALLBACK_BASE_URL', 'https://accesstech.co.ke/mpesa') . '/callbacks/stk_callback.php',
        'c2b_validation' => env('CALLBACK_BASE_URL', 'https://accesstech.co.ke/mpesa') . '/callbacks/c2b_validation.php',
        'c2b_confirmation' => env('CALLBACK_BASE_URL', 'https://accesstech.co.ke/mpesa') . '/callbacks/c2b_confirmation.php',
        'b2c_result' => env('CALLBACK_BASE_URL', 'https://accesstech.co.ke/mpesa') . '/callbacks/b2c_result.php',
        'b2c_timeout' => env('CALLBACK_BASE_URL', 'https://accesstech.co.ke/mpesa') . '/callbacks/b2c_timeout.php',
        'transaction_status_result' => env('CALLBACK_BASE_URL', 'https://accesstech.co.ke/mpesa') . '/callbacks/transaction_status.php',
        'transaction_status_timeout' => env('CALLBACK_BASE_URL', 'https://accesstech.co.ke/mpesa') . '/callbacks/transaction_timeout.php',
    ],
    
    // Database configuration
    'database' => [
        'host' => env('DB_HOST', 'localhost'),
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'database' => env('DB_DATABASE', 'qwetfpzv_mpesa'),
    ],
];
