<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Logs - Access Tech Solutions</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .logs-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .logs-header {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
        }
        .logs-grid {
            display: grid;
            gap: 1.5rem;
        }
        .log-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: var(--shadow);
        }
        .log-card h3 {
            margin-bottom: 1rem;
            color: var(--dark-color);
        }
        .log-content {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
            max-height: 400px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            font-size: 0.75rem;
            line-height: 1.5;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .no-logs {
            color: var(--text-muted);
            text-align: center;
            padding: 2rem;
        }
        .log-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            color: var(--text-muted);
        }
        .refresh-btn {
            float: right;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>Access Tech Solutions</h1>
                    <span class="subtitle">Transaction Logs</span>
                </div>
                <nav class="nav">
                    <a href="../index.php" class="nav-link">Back to Demo</a>
                </nav>
            </div>
        </div>
    </header>

    <div class="logs-container">
        <div class="logs-header">
            <h2>Transaction Logs</h2>
            <p>View API requests, responses, and callbacks</p>
            <button onclick="location.reload()" class="btn btn-secondary refresh-btn">Refresh Logs</button>
        </div>

        <div class="logs-grid">
            <?php
            $logsDir = __DIR__ . '/../logs/';
            
            if (!is_dir($logsDir)) {
                echo '<div class="no-logs">Logs directory not found. Logs will be created automatically when you make API requests.</div>';
            } else {
                $logFiles = glob($logsDir . '*.log');
                
                if (empty($logFiles)) {
                    echo '<div class="no-logs">No log files found. Make some API requests to generate logs.</div>';
                } else {
                    // Sort by modification time (newest first)
                    usort($logFiles, function($a, $b) {
                        return filemtime($b) - filemtime($a);
                    });
                    
                    foreach ($logFiles as $logFile) {
                        $fileName = basename($logFile);
                        $fileSize = filesize($logFile);
                        $fileTime = date('Y-m-d H:i:s', filemtime($logFile));
                        $content = file_get_contents($logFile);
                        
                        // Limit content display
                        if (strlen($content) > 10000) {
                            $content = substr($content, 0, 10000) . "\n\n... (file truncated for display, " . number_format(strlen($content)) . " bytes total)";
                        }
                        
                        echo '<div class="log-card">';
                        echo '<h3>' . htmlspecialchars($fileName) . '</h3>';
                        echo '<div class="log-meta">';
                        echo '<span>Last Modified: ' . $fileTime . '</span>';
                        echo '<span>Size: ' . number_format($fileSize) . ' bytes</span>';
                        echo '</div>';
                        
                        if (empty(trim($content))) {
                            echo '<div class="no-logs">File is empty</div>';
                        } else {
                            echo '<div class="log-content">' . htmlspecialchars($content) . '</div>';
                        }
                        
                        echo '</div>';
                    }
                }
            }
            ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2026 Access Tech Solutions LTD. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
