<?php

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

echo "--- STARTING REGISTER OTP BACKEND TEST ---\n\n";

// Let's delete our test user if they already exist
$testEmail = 'bot_test_otp@example.com';
User::where('email', $testEmail)->delete();
echo "1. Cleaned up existing test user (if any).\n";

// Instantiating Controller
$authController = new AuthController();

// 2. Mocking the POST /register request
// Using a real phone number prefix but temporary test phone number
$testPhone = '08218417911'; 
$requestRegister = Request::create('/register', 'POST', [
    'name' => 'OTP Bot Test',
    'email' => $testEmail,
    'phone' => $testPhone,
    'password' => 'password123',
    'password_confirmation' => 'password123',
]);

echo "2. Sending POST /register request...\n";
$response = $authController->register($requestRegister);

// Verify redirect
if ($response->isRedirection()) {
    $redirectUrl = $response->getTargetUrl();
    echo "   - Redirect Target: " . $redirectUrl . "\n";
    
    $session = $app['session.store'];
    if ($session->has('reg_phone')) {
        echo "   - Session reg_phone: " . $session->get('reg_phone') . "\n";
        echo "   - Session reg_email: " . $session->get('reg_email') . "\n";
        echo "   - Session reg_otp_code: " . $session->get('reg_otp_code') . "\n";
        
        // Let's check verify OTP flow!
        $otp = $session->get('reg_otp_code');
        echo "3. Verifying OTP code {$otp}...\n";
        
        $requestVerify = Request::create('/register/verify', 'POST', [
            'otp' => $otp,
        ]);
        $requestVerify->setLaravelSession($session);
        
        $verifyResponse = $authController->verifyOtp($requestVerify);
        
        if ($verifyResponse->isRedirection()) {
            echo "   - Redirected after verify: " . $verifyResponse->getTargetUrl() . "\n";
            // Check if user is created
            $user = User::where('email', $testEmail)->first();
            if ($user) {
                echo "   [SUCCESS] User was successfully created in Database with phone " . $user->phone . "!\n";
                // Cleanup
                $user->delete();
                echo "   - Cleaned up test user.\n";
                exit(0);
            } else {
                echo "   [FAILED] User was not created in database.\n";
            }
        } else {
            echo "   [FAILED] Verify did not redirect. Body: " . $verifyResponse->getContent() . "\n";
        }
    } else {
        echo "   [FAILED] Session does not have registration data.\n";
        if ($session->has('errors')) {
            $errors = $session->get('errors')->getBag('default');
            print_r($errors->all());
        }
    }
} else {
    echo "   [FAILED] Register did not redirect. Body: " . $response->getContent() . "\n";
}

exit(1);
