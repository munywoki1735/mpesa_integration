<?php
/**
 * M-Pesa Daraja API Helper Class
 * 
 * This class handles all M-Pesa API interactions
 */

class MpesaAPI {
    private $config;
    private $env;
    private $credentials;
    private $endpoints;
    private $access_token;
    
    public function __construct($configPath = '../config.php') {
        // Load configuration
        $this->config = require($configPath);
        $this->env = $this->config['environment'];
        $this->credentials = $this->config[$this->env];
        $this->endpoints = $this->config['endpoints'][$this->env];
    }
    
    /**
     * Generate Access Token
     * Required for all API calls
     */
    public function generateAccessToken() {
        $url = $this->endpoints['base_url'] . $this->endpoints['oauth'];
        
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_USERPWD, 
            $this->credentials['consumer_key'] . ':' . $this->credentials['consumer_secret']
        );
        
        $response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if ($status == 200) {
            $result = json_decode($response);
            $this->access_token = $result->access_token;
            return $this->access_token;
        }
        
        throw new Exception("Failed to generate access token: " . $response);
    }
    
    /**
     * STK Push (Lipa Na M-Pesa Online)
     * Initiates a payment request to customer's phone
     * 
     * @param string $phone - Customer phone number (format: 254XXXXXXXXX)
     * @param int $amount - Amount to charge
     * @param string $reference - Account reference
     * @param string $description - Transaction description
     */
    public function stkPush($phone, $amount, $reference, $description) {
        if (!$this->access_token) {
            $this->generateAccessToken();
        }
        
        $url = $this->endpoints['base_url'] . $this->endpoints['stk_push'];
        $timestamp = date('YmdHis');
        $password = base64_encode(
            $this->credentials['shortcode'] . 
            $this->credentials['passkey'] . 
            $timestamp
        );
        
        $data = [
            'BusinessShortCode' => $this->credentials['shortcode'],
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $phone,
            'PartyB' => $this->credentials['shortcode'],
            'PhoneNumber' => $phone,
            'CallBackURL' => $this->config['callback_urls']['stk_callback'],
            'AccountReference' => $reference,
            'TransactionDesc' => $description
        ];
        
        return $this->makeRequest($url, $data);
    }
    
    /**
     * STK Push Query
     * Check the status of an STK Push transaction
     * 
     * @param string $checkoutRequestID - CheckoutRequestID from STK Push response
     */
    public function stkQuery($checkoutRequestID) {
        if (!$this->access_token) {
            $this->generateAccessToken();
        }
        
        $url = $this->endpoints['base_url'] . $this->endpoints['stk_query'];
        $timestamp = date('YmdHis');
        $password = base64_encode(
            $this->credentials['shortcode'] . 
            $this->credentials['passkey'] . 
            $timestamp
        );
        
        $data = [
            'BusinessShortCode' => $this->credentials['shortcode'],
            'Password' => $password,
            'Timestamp' => $timestamp,
            'CheckoutRequestID' => $checkoutRequestID
        ];
        
        return $this->makeRequest($url, $data);
    }
    
    /**
     * C2B Register URLs
     * Register validation and confirmation URLs
     */
    public function c2bRegisterURLs() {
        if (!$this->access_token) {
            $this->generateAccessToken();
        }
        
        $url = $this->endpoints['base_url'] . $this->endpoints['c2b_register'];
        
        $data = [
            'ShortCode' => $this->credentials['shortcode'],
            'ResponseType' => 'Completed', // or 'Cancelled'
            'ConfirmationURL' => $this->config['callback_urls']['c2b_confirmation'],
            'ValidationURL' => $this->config['callback_urls']['c2b_validation']
        ];
        
        return $this->makeRequest($url, $data);
    }
    
    /**
     * C2B Simulate Transaction
     * Simulate a C2B payment (Sandbox only)
     */
    public function c2bSimulate($phone, $amount, $billRefNumber = 'TestAPI') {
        if (!$this->access_token) {
            $this->generateAccessToken();
        }
        
        $url = $this->endpoints['base_url'] . $this->endpoints['c2b_simulate'];
        
        $data = [
            'ShortCode' => $this->credentials['shortcode'],
            'CommandID' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'Msisdn' => $phone,
            'BillRefNumber' => $billRefNumber
        ];
        
        return $this->makeRequest($url, $data);
    }
    
    /**
     * B2C Payment Request
     * Send money from business to customer
     * 
     * @param string $phone - Recipient phone number
     * @param int $amount - Amount to send
     * @param string $occasion - Occasion for payment
     * @param string $remarks - Remarks
     */
    public function b2cPayment($phone, $amount, $occasion = '', $remarks = '') {
        if (!$this->access_token) {
            $this->generateAccessToken();
        }
        
        $url = $this->endpoints['base_url'] . $this->endpoints['b2c'];
        
        $data = [
            'InitiatorName' => $this->credentials['initiator_name'],
            'SecurityCredential' => $this->credentials['security_credential'],
            'CommandID' => 'BusinessPayment', // or 'SalaryPayment', 'PromotionPayment'
            'Amount' => $amount,
            'PartyA' => $this->credentials['shortcode'],
            'PartyB' => $phone,
            'Remarks' => $remarks ?: 'B2C Payment',
            'QueueTimeOutURL' => $this->config['callback_urls']['b2c_timeout'],
            'ResultURL' => $this->config['callback_urls']['b2c_result'],
            'Occasion' => $occasion
        ];
        
        return $this->makeRequest($url, $data);
    }
    
    /**
     * Transaction Status Query
     * Check the status of a transaction
     */
    public function transactionStatus($transactionID) {
        if (!$this->access_token) {
            $this->generateAccessToken();
        }
        
        $url = $this->endpoints['base_url'] . $this->endpoints['transaction_status'];
        
        $data = [
            'Initiator' => $this->credentials['initiator_name'],
            'SecurityCredential' => $this->credentials['security_credential'],
            'CommandID' => 'TransactionStatusQuery',
            'TransactionID' => $transactionID,
            'PartyA' => $this->credentials['shortcode'],
            'IdentifierType' => '4',
            'ResultURL' => $this->config['callback_urls']['transaction_status_result'],
            'QueueTimeOutURL' => $this->config['callback_urls']['transaction_status_timeout'],
            'Remarks' => 'Transaction Status Query',
            'Occasion' => 'Status Check'
        ];
        
        return $this->makeRequest($url, $data);
    }
    
    /**
     * Account Balance Query
     * Check account balance
     */
    public function accountBalance() {
        if (!$this->access_token) {
            $this->generateAccessToken();
        }
        
        $url = $this->endpoints['base_url'] . $this->endpoints['account_balance'];
        
        $data = [
            'Initiator' => $this->credentials['initiator_name'],
            'SecurityCredential' => $this->credentials['security_credential'],
            'CommandID' => 'AccountBalance',
            'PartyA' => $this->credentials['shortcode'],
            'IdentifierType' => '4',
            'Remarks' => 'Account Balance Query',
            'QueueTimeOutURL' => $this->config['callback_urls']['transaction_status_timeout'],
            'ResultURL' => $this->config['callback_urls']['transaction_status_result']
        ];
        
        return $this->makeRequest($url, $data);
    }
    
    /**
     * Make HTTP Request to M-Pesa API
     */
    private function makeRequest($url, $data) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->access_token
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        
        $response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        // Log the request and response
        $this->logTransaction($url, $data, $response, $status);
        
        return [
            'status' => $status,
            'response' => json_decode($response, true)
        ];
    }
    
    /**
     * Log transactions for debugging
     */
    private function logTransaction($url, $request, $response, $status) {
        $logDir = __DIR__ . '/../logs';
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }
        
        $logFile = $logDir . '/mpesa_' . date('Y-m-d') . '.log';
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'url' => $url,
            'status' => $status,
            'request' => $request,
            'response' => $response
        ];
        
        file_put_contents(
            $logFile, 
            json_encode($logData, JSON_PRETTY_PRINT) . "\n\n", 
            FILE_APPEND
        );
    }
    
    /**
     * Validate phone number format
     */
    public static function formatPhoneNumber($phone) {
        // Remove any spaces, dashes, or plus signs
        $phone = preg_replace('/[\s\-\+]/', '', $phone);
        
        // If starts with 0, replace with 254
        if (substr($phone, 0, 1) === '0') {
            $phone = '254' . substr($phone, 1);
        }
        
        // If doesn't start with 254, add it
        if (substr($phone, 0, 3) !== '254') {
            $phone = '254' . $phone;
        }
        
        return $phone;
    }
}
