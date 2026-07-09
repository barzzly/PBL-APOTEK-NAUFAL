<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class WhatsAppAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login-wa');
    }

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

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:8|max:20',
        ]);

        $cleanPhone = $this->normalizePhoneNumber($request->phone);

        if (strlen($cleanPhone) < 10) {
            return back()->withErrors(['phone' => 'Format nomor WhatsApp tidak valid.']);
        }

        // Flexible search in users table to match different phone formats (62..., 0..., +62..., 8...)
        $phoneSuffix = substr($cleanPhone, 2); // get main number without 62 prefix, e.g. 812xxx
        $user = User::where('phone', $cleanPhone)
            ->orWhere('phone', '0' . $phoneSuffix)
            ->orWhere('phone', '+' . $cleanPhone)
            ->orWhere('phone', $phoneSuffix)
            ->first();

        if (!$user) {
            return back()->withErrors(['phone' => 'Nomor WhatsApp Anda belum terdaftar. Silakan daftar terlebih dahulu.']);
        }

        // Generate 6-digit random OTP
        $otp = mt_rand(100000, 999999);

        // Save OTP info to Laravel session
        session([
            'otp_phone' => $cleanPhone,
            'otp_user_id' => $user->id,
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(5)
        ]);

        // Send OTP via Next.js WhatsApp Bot API
        $apiUrl = config('services.whatsapp.api_url') . '/api/send-message';
        $message = "Kode OTP masuk Apotek Naufal Anda adalah: *{$otp}*.\n\nRahasiakan kode ini dari siapa pun. Kode ini hanya berlaku selama 5 menit.";

        try {
            $response = Http::post($apiUrl, [
                'to' => $cleanPhone,
                'message' => $message
            ]);

            if ($response->failed()) {
                $errorMsg = $response->json()['error'] ?? 'Gagal mengirim pesan OTP.';
                return back()->withErrors(['phone' => 'Gagal mengirim OTP: ' . $errorMsg]);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['phone' => 'Gagal terhubung dengan layanan Bot WhatsApp: ' . $e->getMessage()]);
        }

        return redirect()->route('login.wa.verify')->with('status', 'Kode OTP telah berhasil dikirim ke nomor WhatsApp Anda.');
    }

    public function showVerifyForm()
    {
        if (!session()->has('otp_phone')) {
            return redirect()->route('login.wa');
        }

        return view('auth.verify-wa');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
        ]);

        $sessionOtp = session('otp_code');
        $sessionPhone = session('otp_phone');
        $userId = session('otp_user_id');
        $expiresAt = session('otp_expires_at');

        if (!$sessionOtp || !$sessionPhone || !$userId || !$expiresAt) {
            return redirect()->route('login.wa')->withErrors(['phone' => 'Sesi login telah berakhir. Silakan masukkan nomor Anda kembali.']);
        }

        // Check expiration
        if (now()->greaterThan($expiresAt)) {
            return redirect()->route('login.wa')->withErrors(['phone' => 'Kode OTP telah kedaluwarsa. Silakan minta kode baru.']);
        }

        // Check OTP code validity
        if ($request->otp != $sessionOtp) {
            return back()->withErrors(['otp' => 'Kode OTP salah. Silakan periksa kembali.']);
        }

        // Authenticate the user
        Auth::loginUsingId($userId);
        
        $request->session()->regenerate();
        $this->syncSessionCartToDatabase();

        // Clear OTP sessions
        session()->forget(['otp_phone', 'otp_user_id', 'otp_code', 'otp_expires_at']);

        // Redirect based on role
        if (Auth::user()->role === 'admin') {
            return redirect()->intended('/admin/dashboard');
        }

        return redirect()->intended('/');
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
