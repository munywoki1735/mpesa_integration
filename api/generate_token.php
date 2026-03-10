<?php
/**
 * Generate Access Token API Endpoint
 */

header('Content-Type: application/json');

require_once '../includes/MpesaAPI.php';

try {
    // Initialize M-Pesa API
    $mpesa = new MpesaAPI('../config.php');
    
    // Generate token
    $token = $mpesa->generateAccessToken();
    
    echo json_encode([
        'success' => true,
        'token' => $token
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
