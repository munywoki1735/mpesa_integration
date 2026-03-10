<?php
/**
 * .env File Creator
 * Run this script once to create your .env file
 */

$envFile = __DIR__ . '/.env';
$exampleFile = __DIR__ . '/env.example.txt';

echo "======================================\n";
echo "  .env File Creator\n";
echo "  Access Tech Solutions LTD\n";
echo "======================================\n\n";

// Check if .env already exists
if (file_exists($envFile)) {
    echo "✓ .env file already exists!\n";
    echo "  Location: $envFile\n\n";
    echo "To recreate, delete the existing .env file first.\n";
    exit(0);
}

// Check if example file exists
if (!file_exists($exampleFile)) {
    echo "✗ Error: env.example.txt not found!\n";
    exit(1);
}

// Copy example to .env
if (copy($exampleFile, $envFile)) {
    echo "✓ Successfully created .env file!\n";
    echo "  Location: $envFile\n\n";
    
    // Set permissions (Unix/Linux only)
    if (PHP_OS !== 'WINNT') {
        chmod($envFile, 0600);
        echo "✓ File permissions set to 600 (read/write for owner only)\n\n";
    }
    
    echo "Your credentials are already configured:\n";
    echo "  - Consumer Key: zr0mS1...fHws\n";
    echo "  - Consumer Secret: B27BM...gSEY\n";
    echo "  - Environment: sandbox\n\n";
    
    echo "Next steps:\n";
    echo "  1. Verify credentials in .env file\n";
    echo "  2. Test your setup at: http://localhost/mpesa/\n";
    echo "  3. When deploying, update CALLBACK_BASE_URL\n\n";
    
    echo "Security Notes:\n";
    echo "  - .env is protected by .htaccess\n";
    echo "  - .env is in .gitignore (won't be committed)\n";
    echo "  - Never share your .env file\n\n";
    
} else {
    echo "✗ Error: Could not create .env file\n";
    echo "  Manually rename env.example.txt to .env\n";
    exit(1);
}

echo "Setup complete! Ready to use.\n";
?>
