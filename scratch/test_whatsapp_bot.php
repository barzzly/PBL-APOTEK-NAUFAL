<?php

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

echo "--- CHECKING WHATSAPP BOT STATUS ---\n\n";

$apiUrl = config('services.whatsapp.api_url');
echo "Bot API URL: {$apiUrl}\n";

try {
    $response = Http::get($apiUrl . '/api/status');
    echo "HTTP Status Code: " . $response->status() . "\n";
    
    if ($response->successful()) {
        $data = $response->json();
        echo "WhatsApp Connection Status: " . ($data['status'] ?? 'unknown') . "\n";
        echo "Phone Number: " . ($data['phoneNumber'] ?? 'none') . "\n";
        echo "QR Available: " . ($data['qr'] ? 'Yes' : 'No') . "\n";
        echo "Logs:\n";
        foreach (($data['logs'] ?? []) as $log) {
            echo "  {$log}\n";
        }
    } else {
        echo "API returned error: " . $response->body() . "\n";
    }
} catch (\Exception $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
