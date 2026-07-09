<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class AuthController extends Controller
{
    private function normalizePhoneNumber($phone)
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($clean, '0')) {
            $clean = '62' . substr($clean, 1);
        } elseif (str_starts_with($clean, '8')) {
            $clean = '62' . $clean;
        }
        return $clean;
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $cleanPhone = $this->normalizePhoneNumber($request->phone);

        if (strlen($cleanPhone) < 10) {
            return back()->withInput()->withErrors(['phone' => 'Format nomor WhatsApp tidak valid.']);
        }

        // Check if phone number is already registered
        $phoneSuffix = substr($cleanPhone, 2);
        $exists = User::where('phone', $cleanPhone)
            ->orWhere('phone', '0' . $phoneSuffix)
            ->orWhere('phone', '+' . $cleanPhone)
            ->orWhere('phone', $phoneSuffix)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors(['phone' => 'Nomor WhatsApp sudah terdaftar.']);
        }

        // Generate 6-digit random OTP
        $otp = mt_rand(100000, 999999);

        // Save registration data and OTP details to session
        session([
            'reg_name' => $request->name,
            'reg_email' => $request->email,
            'reg_phone' => $cleanPhone,
            'reg_password' => $request->password, // Model casts 'password' => 'hashed' handles hashing automatically
            'reg_otp_code' => $otp,
            'reg_otp_expires_at' => now()->addMinutes(5)
        ]);

        // Send OTP via Next.js WhatsApp Bot API
        $apiUrl = config('services.whatsapp.api_url') . '/api/send-message';
        $message = "Kode OTP pendaftaran akun Apotek Naufal Anda adalah: *{$otp}*.\n\nRahasiakan kode ini dari siapa pun. Kode ini hanya berlaku selama 5 menit.";

        try {
            $response = Http::post($apiUrl, [
                'to' => $cleanPhone,
                'message' => $message
            ]);

            if ($response->failed()) {
                $errorMsg = $response->json()['error'] ?? 'Gagal mengirim pesan OTP.';
                return back()->withInput()->withErrors(['phone' => 'Gagal mengirim OTP: ' . $errorMsg]);
            }
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['phone' => 'Gagal terhubung dengan layanan Bot WhatsApp: ' . $e->getMessage()]);
        }

        return redirect()->route('register.verify')->with('status', 'Kode OTP telah berhasil dikirim ke nomor WhatsApp Anda.');
    }

    public function showVerifyForm()
    {
        if (!session()->has('reg_phone')) {
            return redirect()->route('register');
        }

        return view('auth.verify-register-wa');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
        ]);

        $sessionOtp = session('reg_otp_code');
        $sessionPhone = session('reg_phone');
        $expiresAt = session('reg_otp_expires_at');

        if (!$sessionOtp || !$sessionPhone || !$expiresAt) {
            return redirect()->route('register')->withErrors(['phone' => 'Sesi pendaftaran telah berakhir. Silakan daftar kembali.']);
        }

        // Check expiration
        if (now()->greaterThan($expiresAt)) {
            return redirect()->route('register')->withErrors(['phone' => 'Kode OTP telah kedaluwarsa. Silakan daftar kembali.']);
        }

        // Check OTP code validity
        if ($request->otp != $sessionOtp) {
            return back()->withErrors(['otp' => 'Kode OTP salah. Silakan periksa kembali.']);
        }

        // Create the user
        $user = User::create([
            'name' => session('reg_name'),
            'email' => session('reg_email'),
            'phone' => session('reg_phone'),
            'password' => session('reg_password'), // already hashed
            'role' => 'customer',
        ]);

        // Authenticate the user
        Auth::login($user);
        
        $request->session()->regenerate();
        $this->syncSessionCartToDatabase();

        // Clear registration sessions
        session()->forget([
            'reg_name',
            'reg_email',
            'reg_phone',
            'reg_password',
            'reg_otp_code',
            'reg_otp_expires_at'
        ]);

        return redirect('/');
    }

    public function resendOtp(Request $request)
    {
        $cleanPhone = session('reg_phone');

        if (!$cleanPhone) {
            return redirect()->route('register')->withErrors(['phone' => 'Sesi pendaftaran tidak ditemukan. Silakan daftar kembali.']);
        }

        // Generate new 6-digit random OTP
        $otp = mt_rand(100000, 999999);

        // Update OTP in session
        session([
            'reg_otp_code' => $otp,
            'reg_otp_expires_at' => now()->addMinutes(5)
        ]);

        // Send OTP via Next.js WhatsApp Bot API
        $apiUrl = config('services.whatsapp.api_url') . '/api/send-message';
        $message = "Kode OTP pendaftaran akun Apotek Naufal Anda yang baru adalah: *{$otp}*.\n\nRahasiakan kode ini dari siapa pun. Kode ini hanya berlaku selama 5 menit.";

        try {
            $response = Http::post($apiUrl, [
                'to' => $cleanPhone,
                'message' => $message
            ]);

            if ($response->failed()) {
                $errorMsg = $response->json()['error'] ?? 'Gagal mengirim pesan OTP.';
                return back()->withErrors(['otp' => 'Gagal mengirim ulang OTP: ' . $errorMsg]);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['otp' => 'Gagal terhubung dengan layanan Bot WhatsApp: ' . $e->getMessage()]);
        }

        return back()->with('status', 'Kode OTP baru telah berhasil dikirim ke nomor WhatsApp Anda.');
    }

    public function login(Request $request)
    {
        $loginInput = $request->input('email'); // Form field name is "email"
        $password = $request->input('password');

        // Check if input is email format
        $loginField = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        
        if ($loginField === 'phone') {
            $cleanPhone = $this->normalizePhoneNumber($loginInput);
            $phoneSuffix = substr($cleanPhone, 2);
            
            // Try to find user by any phone format or fallback to email (if 'admin' etc.)
            $user = User::where('phone', $cleanPhone)
                ->orWhere('phone', '0' . $phoneSuffix)
                ->orWhere('phone', '+' . $cleanPhone)
                ->orWhere('phone', $phoneSuffix)
                ->first();
                
            if ($user) {
                $credentials = ['phone' => $user->phone, 'password' => $password];
            } else {
                // Fallback to checking email column (for 'admin')
                $credentials = ['email' => $loginInput, 'password' => $password];
            }
        } else {
            $credentials = ['email' => $loginInput, 'password' => $password];
        }

        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();
            $this->syncSessionCartToDatabase();
            
            if (Auth::user()->role === 'admin') {
                return redirect()->intended('/admin/dashboard');
            }
            
            return redirect()->intended('/');
        }
        
        return back()->withInput()->withErrors([
            'email' => 'Email/Username atau password salah.',
        ]);
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    private function syncSessionCartToDatabase()
    {
        if (session()->has('cart')) {
            $sessionCart = session()->get('cart', []);
            foreach ($sessionCart as $medicineId => $item) {
                $cartItem = \App\Models\CartItem::where('user_id', Auth::id())
                    ->where('medicine_id', $medicineId)
                    ->first();
                
                if ($cartItem) {
                    $newQty = $cartItem->quantity + $item['quantity'];
                    $medicine = \App\Models\Medicine::find($medicineId);
                    if ($medicine && $newQty > $medicine->stock) {
                        $newQty = $medicine->stock;
                    }
                    $cartItem->update(['quantity' => $newQty]);
                } else {
                    \App\Models\CartItem::create([
                        'user_id' => Auth::id(),
                        'medicine_id' => $medicineId,
                        'quantity' => $item['quantity'],
                    ]);
                }
            }
            session()->forget('cart');
        }
    }
}
