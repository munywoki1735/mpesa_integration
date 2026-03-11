<?php
/**
 * M-Pesa Daraja API Configuration
 * Access Tech Solutions LTD
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
