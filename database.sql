-- ============================================================
-- M-Pesa Integration Database Schema
-- Access Tech Solutions LTD
-- Database: qwetfpzv_mpesa
-- ============================================================

-- Create database (if not exists)
CREATE DATABASE IF NOT EXISTS `qwetfpzv_mpesa` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE `qwetfpzv_mpesa`;

-- ============================================================
-- Table: stk_transactions
-- Purpose: Store STK Push (Lipa Na M-Pesa Online) transactions
-- ============================================================

CREATE TABLE IF NOT EXISTS `stk_transactions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `merchant_request_id` VARCHAR(100) NULL,
  `checkout_request_id` VARCHAR(100) NULL UNIQUE,
  `phone_number` VARCHAR(15) NOT NULL,
  `amount` DECIMAL(10, 2) NOT NULL,
  `account_reference` VARCHAR(100) NOT NULL,
  `transaction_desc` TEXT NULL,
  `mpesa_receipt_number` VARCHAR(50) NULL,
  `transaction_date` VARCHAR(20) NULL,
  `status` ENUM('pending', 'completed', 'failed', 'cancelled', 'timeout') DEFAULT 'pending',
  `result_code` INT NULL,
  `result_desc` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_phone` (`phone_number`),
  INDEX `idx_status` (`status`),
  INDEX `idx_created` (`created_at`),
  INDEX `idx_checkout` (`checkout_request_id`),
  INDEX `idx_merchant` (`merchant_request_id`),
  INDEX `idx_mpesa_receipt` (`mpesa_receipt_number`),
  INDEX `idx_account_ref` (`account_reference`),
  INDEX `idx_status_created` (`status`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: c2b_transactions
-- Purpose: Store C2B (Customer to Business) transactions
-- ============================================================

CREATE TABLE IF NOT EXISTS `c2b_transactions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `trans_id` VARCHAR(50) NOT NULL UNIQUE,
  `trans_time` VARCHAR(20) NULL,
  `trans_amount` DECIMAL(10, 2) NOT NULL,
  `business_short_code` VARCHAR(20) NULL,
  `bill_ref_number` VARCHAR(100) NULL,
  `invoice_number` VARCHAR(100) NULL,
  `org_account_balance` DECIMAL(15, 2) NULL,
  `third_party_trans_id` VARCHAR(50) NULL,
  `msisdn` VARCHAR(15) NOT NULL,
  `first_name` VARCHAR(100) NULL,
  `middle_name` VARCHAR(100) NULL,
  `last_name` VARCHAR(100) NULL,
  `transaction_type` VARCHAR(50) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_trans_id` (`trans_id`),
  INDEX `idx_msisdn` (`msisdn`),
  INDEX `idx_bill_ref` (`bill_ref_number`),
  INDEX `idx_created` (`created_at`),
  INDEX `idx_amount` (`trans_amount`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: b2c_transactions
-- Purpose: Store B2C (Business to Customer) transactions
-- ============================================================

CREATE TABLE IF NOT EXISTS `b2c_transactions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `conversation_id` VARCHAR(100) NULL UNIQUE,
  `originator_conversation_id` VARCHAR(100) NULL,
  `recipient_phone` VARCHAR(15) NOT NULL,
  `amount` DECIMAL(10, 2) NOT NULL,
  `command_id` VARCHAR(50) NULL,
  `remarks` TEXT NULL,
  `occasion` VARCHAR(100) NULL,
  `transaction_id` VARCHAR(50) NULL,
  `transaction_receipt` VARCHAR(50) NULL,
  `b2c_working_account_available_funds` DECIMAL(15, 2) NULL,
  `b2c_utility_account_available_funds` DECIMAL(15, 2) NULL,
  `transaction_completed_datetime` VARCHAR(50) NULL,
  `receiver_party_public_name` VARCHAR(255) NULL,
  `b2c_charges_paid_account_available_funds` DECIMAL(15, 2) NULL,
  `result_code` INT NULL,
  `result_desc` TEXT NULL,
  `status` ENUM('pending', 'completed', 'failed', 'timeout') DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_conversation` (`conversation_id`),
  INDEX `idx_recipient` (`recipient_phone`),
  INDEX `idx_status` (`status`),
  INDEX `idx_created` (`created_at`),
  INDEX `idx_transaction_id` (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: transaction_logs
-- Purpose: Store all API requests and responses for debugging
-- ============================================================

CREATE TABLE IF NOT EXISTS `transaction_logs` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `transaction_type` VARCHAR(50) NOT NULL,
  `request_data` LONGTEXT NULL,
  `response_data` LONGTEXT NULL,
  `status_code` INT NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_type` (`transaction_type`),
  INDEX `idx_created` (`created_at`),
  INDEX `idx_status` (`status_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: api_tokens
-- Purpose: Store and track access tokens
-- ============================================================

CREATE TABLE IF NOT EXISTS `api_tokens` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `token` TEXT NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `environment` ENUM('sandbox', 'live') DEFAULT 'sandbox',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_expires` (`expires_at`),
  INDEX `idx_env` (`environment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: callback_logs
-- Purpose: Store all callback data received from M-Pesa
-- ============================================================

CREATE TABLE IF NOT EXISTS `callback_logs` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `callback_type` VARCHAR(50) NOT NULL,
  `callback_data` LONGTEXT NOT NULL,
  `processed` TINYINT(1) DEFAULT 0,
  `processed_at` DATETIME NULL,
  `error_message` TEXT NULL,
  `ip_address` VARCHAR(45) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_type` (`callback_type`),
  INDEX `idx_processed` (`processed`),
  INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: customers
-- Purpose: Store customer information
-- ============================================================

CREATE TABLE IF NOT EXISTS `customers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `phone_number` VARCHAR(15) NOT NULL UNIQUE,
  `first_name` VARCHAR(100) NULL,
  `middle_name` VARCHAR(100) NULL,
  `last_name` VARCHAR(100) NULL,
  `email` VARCHAR(255) NULL,
  `total_transactions` INT DEFAULT 0,
  `total_amount` DECIMAL(15, 2) DEFAULT 0.00,
  `last_transaction_at` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_phone` (`phone_number`),
  INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: reconciliation
-- Purpose: Daily reconciliation records
-- ============================================================

CREATE TABLE IF NOT EXISTS `reconciliation` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `date` DATE NOT NULL UNIQUE,
  `total_transactions` INT DEFAULT 0,
  `successful_transactions` INT DEFAULT 0,
  `failed_transactions` INT DEFAULT 0,
  `total_amount` DECIMAL(15, 2) DEFAULT 0.00,
  `expected_amount` DECIMAL(15, 2) NULL,
  `variance` DECIMAL(15, 2) NULL,
  `reconciled` TINYINT(1) DEFAULT 0,
  `reconciled_at` DATETIME NULL,
  `reconciled_by` VARCHAR(100) NULL,
  `notes` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_date` (`date`),
  INDEX `idx_reconciled` (`reconciled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: settings
-- Purpose: Application settings and configurations
-- ============================================================

CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT NULL,
  `setting_type` VARCHAR(50) DEFAULT 'string',
  `description` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Insert default settings
-- ============================================================

INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `description`) VALUES
('environment', 'sandbox', 'string', 'Current M-Pesa environment (sandbox or live)'),
('paybill_number', '174379', 'string', 'Current PayBill/Till number'),
('auto_reconcile', '1', 'boolean', 'Enable automatic daily reconciliation'),
('callback_retry_attempts', '3', 'integer', 'Number of retry attempts for failed callbacks'),
('transaction_timeout', '120', 'integer', 'Transaction timeout in seconds')
ON DUPLICATE KEY UPDATE setting_value=setting_value;

-- ============================================================
-- Views for reporting
-- ============================================================

-- Daily transaction summary
CREATE OR REPLACE VIEW `daily_summary` AS
SELECT 
    DATE(created_at) as transaction_date,
    COUNT(*) as total_transactions,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as successful,
    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as total_amount,
    AVG(CASE WHEN status = 'completed' THEN amount ELSE NULL END) as avg_amount,
    MIN(amount) as min_amount,
    MAX(amount) as max_amount
FROM stk_transactions
GROUP BY DATE(created_at)
ORDER BY transaction_date DESC;

-- Monthly transaction summary
CREATE OR REPLACE VIEW `monthly_summary` AS
SELECT 
    DATE_FORMAT(created_at, '%Y-%m') as month,
    COUNT(*) as total_transactions,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as successful,
    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
    SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as total_amount
FROM stk_transactions
GROUP BY DATE_FORMAT(created_at, '%Y-%m')
ORDER BY month DESC;

-- Top customers
CREATE OR REPLACE VIEW `top_customers` AS
SELECT 
    phone_number,
    CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as customer_name,
    total_transactions,
    total_amount,
    last_transaction_at
FROM customers
ORDER BY total_amount DESC
LIMIT 100;

-- Recent failed transactions
CREATE OR REPLACE VIEW `recent_failed_transactions` AS
SELECT 
    id,
    phone_number,
    amount,
    account_reference,
    result_code,
    result_desc,
    created_at
FROM stk_transactions
WHERE status = 'failed'
ORDER BY created_at DESC
LIMIT 50;

-- Pending transactions (older than 5 minutes)
CREATE OR REPLACE VIEW `stuck_transactions` AS
SELECT 
    id,
    checkout_request_id,
    phone_number,
    amount,
    account_reference,
    created_at,
    TIMESTAMPDIFF(MINUTE, created_at, NOW()) as minutes_pending
FROM stk_transactions
WHERE status = 'pending'
AND created_at < DATE_SUB(NOW(), INTERVAL 5 MINUTE)
ORDER BY created_at ASC;

-- ============================================================
-- Stored Procedures
-- ============================================================

DELIMITER $$

-- Get transaction statistics for a date range
CREATE PROCEDURE IF NOT EXISTS `GetTransactionStats`(
    IN start_date DATE,
    IN end_date DATE
)
BEGIN
    SELECT 
        COUNT(*) as total_transactions,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as successful,
        SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as total_revenue,
        AVG(CASE WHEN status = 'completed' THEN amount ELSE NULL END) as avg_transaction,
        MIN(CASE WHEN status = 'completed' THEN amount ELSE NULL END) as min_transaction,
        MAX(amount) as max_transaction
    FROM stk_transactions
    WHERE DATE(created_at) BETWEEN start_date AND end_date;
END$$

-- Update customer statistics
CREATE PROCEDURE IF NOT EXISTS `UpdateCustomerStats`(
    IN customer_phone VARCHAR(15)
)
BEGIN
    INSERT INTO customers (phone_number, total_transactions, total_amount, last_transaction_at)
    SELECT 
        phone_number,
        COUNT(*) as total_transactions,
        SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as total_amount,
        MAX(created_at) as last_transaction_at
    FROM stk_transactions
    WHERE phone_number = customer_phone
    AND status = 'completed'
    GROUP BY phone_number
    ON DUPLICATE KEY UPDATE
        total_transactions = VALUES(total_transactions),
        total_amount = VALUES(total_amount),
        last_transaction_at = VALUES(last_transaction_at);
END$$

-- Daily reconciliation
CREATE PROCEDURE IF NOT EXISTS `DailyReconciliation`(
    IN reconcile_date DATE
)
BEGIN
    INSERT INTO reconciliation (
        date,
        total_transactions,
        successful_transactions,
        failed_transactions,
        total_amount,
        reconciled
    )
    SELECT 
        DATE(created_at) as date,
        COUNT(*) as total_transactions,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as successful_transactions,
        SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_transactions,
        SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as total_amount,
        0 as reconciled
    FROM stk_transactions
    WHERE DATE(created_at) = reconcile_date
    GROUP BY DATE(created_at)
    ON DUPLICATE KEY UPDATE
        total_transactions = VALUES(total_transactions),
        successful_transactions = VALUES(successful_transactions),
        failed_transactions = VALUES(failed_transactions),
        total_amount = VALUES(total_amount);
END$$

DELIMITER ;

-- ============================================================
-- Sample queries (commented out)
-- ============================================================

-- Get today's transactions
-- SELECT * FROM stk_transactions WHERE DATE(created_at) = CURDATE();

-- Get transaction by phone number
-- SELECT * FROM stk_transactions WHERE phone_number = '254712345678' ORDER BY created_at DESC;

-- Get today's revenue
-- SELECT SUM(amount) as today_revenue FROM stk_transactions WHERE status = 'completed' AND DATE(created_at) = CURDATE();

-- Get failed transactions
-- SELECT * FROM stk_transactions WHERE status = 'failed' ORDER BY created_at DESC LIMIT 10;

-- Get transaction by M-Pesa receipt
-- SELECT * FROM stk_transactions WHERE mpesa_receipt_number = 'LGR019G3J2';

-- Get daily summary
-- SELECT * FROM daily_summary LIMIT 30;

-- Get monthly summary
-- SELECT * FROM monthly_summary LIMIT 12;

-- Get top customers
-- SELECT * FROM top_customers LIMIT 10;

-- Get stuck transactions
-- SELECT * FROM stuck_transactions;

-- Run reconciliation for today
-- CALL DailyReconciliation(CURDATE());

-- Get transaction stats for date range
-- CALL GetTransactionStats('2026-01-01', '2026-01-31');

-- Update customer stats
-- CALL UpdateCustomerStats('254712345678');

-- ============================================================
-- Database setup complete
-- ============================================================

-- Show tables
SHOW TABLES;

-- Display success message
SELECT 'Database qwetfpzv_mpesa created successfully!' as Status;
SELECT 'Tables created: 9' as Info;
SELECT 'Views created: 5' as Info;
SELECT 'Stored Procedures created: 3' as Info;
