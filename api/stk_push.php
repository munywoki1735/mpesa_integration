<?php
/**
 * STK Push API Endpoint
 */

header('Content-Type: application/json');

require_once '../includes/MpesaAPI.php';

try {
    // Get form data
    $phone = $_POST['phone'] ?? '';
    $amount = $_POST['amount'] ?? 0;
    $reference = $_POST['reference'] ?? '';
    $description = $_POST['description'] ?? '';
    
    // Validate inputs
    if (empty($phone) || empty($amount) || empty($reference)) {
        throw new Exception('Missing required fields');
    }
    
    // Initialize M-Pesa API
    $mpesa = new MpesaAPI('../config.php');
    
    // Format phone number
    $phone = MpesaAPI::formatPhoneNumber($phone);
    
    // Initiate STK Push
    $result = $mpesa->stkPush($phone, $amount, $reference, $description);
    
    echo json_encode($result);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
