<?php
/**
 * STK Push Query API Endpoint
 */

header('Content-Type: application/json');

require_once '../includes/MpesaAPI.php';

try {
    // Get checkout request ID
    $checkoutRequestID = $_POST['checkout_id'] ?? '';
    
    if (empty($checkoutRequestID)) {
        throw new Exception('CheckoutRequestID is required');
    }
    
    // Initialize M-Pesa API
    $mpesa = new MpesaAPI('../config.php');
    
    // Query STK Push status
    $result = $mpesa->stkQuery($checkoutRequestID);
    
    echo json_encode($result);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
