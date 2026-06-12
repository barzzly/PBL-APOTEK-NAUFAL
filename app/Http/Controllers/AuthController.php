<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'customer',
        ]);

        Auth::login($user);
        $this->syncSessionCartToDatabase();

        return redirect('/');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $this->syncSessionCartToDatabase();
            
            if (Auth::user()->role === 'admin') {
                return redirect()->intended('/admin/dashboard');
            }
            
            return redirect()->intended('/');
        }
        
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
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
