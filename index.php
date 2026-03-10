<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M-Pesa Integration Demo - Access Tech Solutions</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>Access Tech Solutions</h1>
                    <span class="subtitle">M-Pesa Integration Platform</span>
                </div>
                <nav class="nav">
                    <a href="#features" class="nav-link">Features</a>
                    <a href="#demo" class="nav-link">Live Demo</a>
                    <a href="https://developer.safaricom.co.ke" target="_blank" class="nav-link">Documentation</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h2>M-Pesa Daraja API Integration</h2>
                <p class="lead">Seamlessly integrate M-Pesa payments into your business applications. Accept payments, send money, and manage transactions with ease.</p>
                <div class="hero-buttons">
                    <a href="#demo" class="btn btn-primary">Try Live Demo</a>
                    <a href="docs.php" class="btn btn-secondary">View Documentation</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features">
        <div class="container">
            <h2 class="section-title">Key Features</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 1l4 4-4 4M3 11V9a4 4 0 014-4h14M7 23l-4-4 4-4M21 13v2a4 4 0 01-4 4H3"></path>
                        </svg>
                    </div>
                    <h3>STK Push</h3>
                    <p>Send payment requests directly to customer phones. Instant payment prompts with PIN entry.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10"></path>
                        </svg>
                    </div>
                    <h3>C2B Payments</h3>
                    <p>Accept payments from customers to your PayBill or Till number with validation and confirmation.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3>Real-time Status</h3>
                    <p>Query transaction status in real-time. Track payment progress and get instant callbacks.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3>Secure & Reliable</h3>
                    <p>Built with security best practices. Comprehensive logging and error handling included.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Demo Section -->
    <section id="demo" class="demo-section">
        <div class="container">
            <h2 class="section-title">Live Demonstration</h2>
            <p class="section-subtitle">Test our M-Pesa integration capabilities in real-time</p>
            
            <div class="demo-grid">
                <!-- STK Push Demo -->
                <div class="demo-card">
                    <div class="demo-header">
                        <h3>STK Push - Lipa Na M-Pesa</h3>
                        <span class="badge">Most Popular</span>
                    </div>
                    <p class="demo-description">Initiate a payment request to a customer's phone number.</p>
                    
                    <form id="stkForm" class="demo-form">
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="phone" placeholder="254XXXXXXXXX or 07XXXXXXXX" required>
                            <small>Format: 254712345678 or 0712345678</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Amount (KES)</label>
                            <input type="number" name="amount" placeholder="10" min="1" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Account Reference</label>
                            <input type="text" name="reference" placeholder="INV-001" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Description</label>
                            <input type="text" name="description" placeholder="Payment for services" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">
                            <span class="btn-text">Send Payment Request</span>
                            <span class="btn-loader" style="display: none;">Processing...</span>
                        </button>
                    </form>
                    
                    <div class="result" id="stkResult"></div>
                </div>

                <!-- STK Query Demo -->
                <div class="demo-card">
                    <div class="demo-header">
                        <h3>Transaction Status Query</h3>
                    </div>
                    <p class="demo-description">Check the status of a previous STK Push transaction.</p>
                    
                    <form id="queryForm" class="demo-form">
                        <div class="form-group">
                            <label>CheckoutRequestID</label>
                            <input type="text" name="checkout_id" placeholder="ws_CO_DMZ_123456789_12345678901234" required>
                            <small>Obtained from STK Push response</small>
                        </div>
                        
                        <button type="submit" class="btn btn-secondary btn-block">
                            <span class="btn-text">Check Status</span>
                            <span class="btn-loader" style="display: none;">Checking...</span>
                        </button>
                    </form>
                    
                    <div class="result" id="queryResult"></div>
                </div>

                <!-- Token Generation -->
                <div class="demo-card">
                    <div class="demo-header">
                        <h3>Access Token Generation</h3>
                    </div>
                    <p class="demo-description">Test API authentication by generating an access token.</p>
                    
                    <form id="tokenForm" class="demo-form">
                        <button type="submit" class="btn btn-secondary btn-block">
                            <span class="btn-text">Generate Token</span>
                            <span class="btn-loader" style="display: none;">Generating...</span>
                        </button>
                    </form>
                    
                    <div class="result" id="tokenResult"></div>
                </div>

                <!-- C2B Registration -->
                <div class="demo-card">
                    <div class="demo-header">
                        <h3>C2B URL Registration</h3>
                    </div>
                    <p class="demo-description">Register validation and confirmation URLs for C2B payments.</p>
                    
                    <form id="c2bRegisterForm" class="demo-form">
                        <button type="submit" class="btn btn-secondary btn-block">
                            <span class="btn-text">Register URLs</span>
                            <span class="btn-loader" style="display: none;">Registering...</span>
                        </button>
                    </form>
                    
                    <div class="result" id="c2bRegisterResult"></div>
                </div>

                <!-- C2B Simulation -->
                <div class="demo-card">
                    <div class="demo-header">
                        <h3>C2B Payment Simulation</h3>
                        <span class="badge badge-info">Sandbox Only</span>
                    </div>
                    <p class="demo-description">Simulate a customer payment to your business number.</p>
                    
                    <form id="c2bSimulateForm" class="demo-form">
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="phone" placeholder="254XXXXXXXXX" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Amount (KES)</label>
                            <input type="number" name="amount" placeholder="100" min="1" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Bill Reference Number</label>
                            <input type="text" name="bill_ref" placeholder="Account123" required>
                        </div>
                        
                        <button type="submit" class="btn btn-secondary btn-block">
                            <span class="btn-text">Simulate Payment</span>
                            <span class="btn-loader" style="display: none;">Simulating...</span>
                        </button>
                    </form>
                    
                    <div class="result" id="c2bSimulateResult"></div>
                </div>

                <!-- View Logs -->
                <div class="demo-card">
                    <div class="demo-header">
                        <h3>Transaction Logs</h3>
                    </div>
                    <p class="demo-description">View detailed logs of all API requests and responses.</p>
                    
                    <div class="demo-form">
                        <a href="admin/logs.php" class="btn btn-secondary btn-block" target="_blank">
                            View Transaction Logs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Info Section -->
    <section class="info-section">
        <div class="container">
            <div class="info-box">
                <h3>Testing Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <strong>Environment:</strong>
                        <span class="badge badge-warning">Sandbox</span>
                    </div>
                    <div class="info-item">
                        <strong>Test Phone Numbers:</strong>
                        <span>254708374149, 254712345678</span>
                    </div>
                    <div class="info-item">
                        <strong>Test Amounts:</strong>
                        <span>1 - 70,000 KES</span>
                    </div>
                    <div class="info-item">
                        <strong>Shortcode:</strong>
                        <span>174379 (Sandbox)</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>Access Tech Solutions LTD</h4>
                    <p>Professional M-Pesa integration solutions for modern businesses.</p>
                </div>
                <div class="footer-section">
                    <h4>Resources</h4>
                    <ul>
                        <li><a href="docs.php">Documentation</a></li>
                        <li><a href="examples/stk_push.php">Code Examples</a></li>
                        <li><a href="https://developer.safaricom.co.ke" target="_blank">Daraja Portal</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact</h4>
                    <p>Email: info@accesstech.co.ke</p>
                    <p>Website: www.accesstech.co.ke</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Access Tech Solutions LTD. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>
