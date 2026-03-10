<?php
/**
 * Database Helper Class (Optional)
 * 
 * Use this class to store M-Pesa transactions in MySQL database
 * First, create the database using database.sql
 */

class Database {
    private $conn;
    private $config;
    
    public function __construct($configPath = '../config.php') {
        $this->config = require($configPath);
        $this->connect();
    }
    
    /**
     * Connect to database
     */
    private function connect() {
        $db = $this->config['database'];
        
        try {
            $this->conn = new mysqli(
                $db['host'],
                $db['username'],
                $db['password'],
                $db['database']
            );
            
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
            
            $this->conn->set_charset("utf8mb4");
            
        } catch (Exception $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Save STK Push request
     */
    public function saveSTKRequest($data) {
        $sql = "INSERT INTO stk_transactions 
                (merchant_request_id, checkout_request_id, phone_number, amount, 
                 account_reference, transaction_desc, status) 
                VALUES (?, ?, ?, ?, ?, ?, 'pending')";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sssdss",
            $data['merchant_request_id'],
            $data['checkout_request_id'],
            $data['phone_number'],
            $data['amount'],
            $data['account_reference'],
            $data['transaction_desc']
        );
        
        return $stmt->execute();
    }
    
    /**
     * Update STK transaction with callback data
     */
    public function updateSTKCallback($data) {
        $sql = "UPDATE stk_transactions 
                SET mpesa_receipt_number = ?, 
                    transaction_date = ?,
                    result_code = ?,
                    result_desc = ?,
                    status = ?
                WHERE checkout_request_id = ?";
        
        $status = ($data['result_code'] == 0) ? 'completed' : 'failed';
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "ssisss",
            $data['mpesa_receipt_number'],
            $data['transaction_date'],
            $data['result_code'],
            $data['result_desc'],
            $status,
            $data['checkout_request_id']
        );
        
        return $stmt->execute();
    }
    
    /**
     * Save C2B transaction
     */
    public function saveC2BTransaction($data) {
        $sql = "INSERT INTO c2b_transactions 
                (trans_id, trans_time, trans_amount, business_short_code, 
                 bill_ref_number, invoice_number, org_account_balance, 
                 third_party_trans_id, msisdn, first_name, middle_name, last_name) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "ssdsssdsssss",
            $data['trans_id'],
            $data['trans_time'],
            $data['trans_amount'],
            $data['business_short_code'],
            $data['bill_ref_number'],
            $data['invoice_number'],
            $data['org_account_balance'],
            $data['third_party_trans_id'],
            $data['msisdn'],
            $data['first_name'],
            $data['middle_name'],
            $data['last_name']
        );
        
        return $stmt->execute();
    }
    
    /**
     * Save B2C request
     */
    public function saveB2CRequest($data) {
        $sql = "INSERT INTO b2c_transactions 
                (conversation_id, originator_conversation_id, recipient_phone, 
                 amount, command_id, remarks, occasion, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sssdsss",
            $data['conversation_id'],
            $data['originator_conversation_id'],
            $data['recipient_phone'],
            $data['amount'],
            $data['command_id'],
            $data['remarks'],
            $data['occasion']
        );
        
        return $stmt->execute();
    }
    
    /**
     * Update B2C with result
     */
    public function updateB2CResult($data) {
        $sql = "UPDATE b2c_transactions 
                SET transaction_id = ?,
                    transaction_receipt = ?,
                    result_code = ?,
                    result_desc = ?,
                    status = ?,
                    transaction_completed_datetime = ?
                WHERE conversation_id = ?";
        
        $status = ($data['result_code'] == 0) ? 'completed' : 'failed';
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "ssissss",
            $data['transaction_id'],
            $data['transaction_receipt'],
            $data['result_code'],
            $data['result_desc'],
            $status,
            $data['transaction_completed_datetime'],
            $data['conversation_id']
        );
        
        return $stmt->execute();
    }
    
    /**
     * Get transaction by checkout request ID
     */
    public function getSTKTransaction($checkoutRequestID) {
        $sql = "SELECT * FROM stk_transactions WHERE checkout_request_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $checkoutRequestID);
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    /**
     * Get transactions by phone number
     */
    public function getTransactionsByPhone($phone, $limit = 10) {
        $sql = "SELECT * FROM stk_transactions 
                WHERE phone_number = ? 
                ORDER BY created_at DESC 
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $phone, $limit);
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get today's statistics
     */
    public function getTodayStats() {
        $sql = "SELECT 
                    COUNT(*) as total_transactions,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as successful,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
                    SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as total_amount
                FROM stk_transactions
                WHERE DATE(created_at) = CURDATE()";
        
        $result = $this->conn->query($sql);
        return $result->fetch_assoc();
    }
    
    /**
     * Log API request/response
     */
    public function logTransaction($type, $request, $response, $statusCode) {
        $sql = "INSERT INTO transaction_logs 
                (transaction_type, request_data, response_data, status_code) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $requestJson = json_encode($request);
        $responseJson = is_string($response) ? $response : json_encode($response);
        
        $stmt->bind_param("sssi", $type, $requestJson, $responseJson, $statusCode);
        return $stmt->execute();
    }
    
    /**
     * Close connection
     */
    public function close() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
    
    /**
     * Destructor
     */
    public function __destruct() {
        $this->close();
    }
}

/**
 * Example Usage:
 * 
 * // Initialize database
 * $db = new Database('config.php');
 * 
 * // Save STK request
 * $db->saveSTKRequest([
 *     'merchant_request_id' => 'MRQ123',
 *     'checkout_request_id' => 'CHK123',
 *     'phone_number' => '254712345678',
 *     'amount' => 1000,
 *     'account_reference' => 'INV001',
 *     'transaction_desc' => 'Payment'
 * ]);
 * 
 * // Update with callback
 * $db->updateSTKCallback([
 *     'checkout_request_id' => 'CHK123',
 *     'mpesa_receipt_number' => 'LGR123ABC',
 *     'transaction_date' => '20230101120000',
 *     'result_code' => 0,
 *     'result_desc' => 'Success'
 * ]);
 * 
 * // Get transaction
 * $transaction = $db->getSTKTransaction('CHK123');
 * 
 * // Get stats
 * $stats = $db->getTodayStats();
 */
