<?php
/**
 * C2B Register URLs API Endpoint
 */

header('Content-Type: application/json');

require_once '../includes/MpesaAPI.php';

try {
    // Initialize M-Pesa API
    $mpesa = new MpesaAPI('../config.php');
    
    // Register URLs
    $result = $mpesa->c2bRegisterURLs();
    
    echo json_encode($result);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
