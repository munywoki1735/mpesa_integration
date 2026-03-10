<?php
/**
 * C2B Simulate API Endpoint
 */

header('Content-Type: application/json');

require_once '../includes/MpesaAPI.php';

try {
    // Get form data
    $phone = $_POST['phone'] ?? '';
    $amount = $_POST['amount'] ?? 0;
    $billRef = $_POST['bill_ref'] ?? 'TestAPI';
    
    if (empty($phone) || empty($amount)) {
        throw new Exception('Phone and amount are required');
    }
    
    // Initialize M-Pesa API
    $mpesa = new MpesaAPI('../config.php');
    
    // Format phone number
    $phone = MpesaAPI::formatPhoneNumber($phone);
    
    // Simulate C2B payment
    $result = $mpesa->c2bSimulate($phone, $amount, $billRef);
    
    echo json_encode($result);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
