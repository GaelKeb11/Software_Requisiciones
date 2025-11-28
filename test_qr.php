<?php

require __DIR__ . '/vendor/autoload.php';

use PragmaRX\Google2FAQRCode\Google2FA;

$google2fa = new Google2FA();

try {
    $inlineUrl = $google2fa->getQRCodeInline(
        'Company Name',
        'email@example.com',
        'SECRETKEY12345'
    );
    
    echo "QR Code generation successful.\n";
    echo substr($inlineUrl, 0, 50) . "...\n";
} catch (\Exception $e) {
    echo "Error generating QR code: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

