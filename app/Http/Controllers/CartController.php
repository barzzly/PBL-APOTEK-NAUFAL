<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Category;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // Fetch cart items dynamically (from DB or Session)
    public function getCartItems()
    {
        if (auth()->check()) {
            $dbItems = CartItem::with('medicine')->where('user_id', auth()->id())->get();
            $cart = [];
            foreach ($dbItems as $dbItem) {
                if ($dbItem->medicine) {
                    $cart[$dbItem->medicine_id] = [
                        'id' => $dbItem->medicine->id,
                        'name' => $dbItem->medicine->name,
                        'price' => (float) $dbItem->medicine->price,
                        'quantity' => $dbItem->quantity,
                        'image' => $dbItem->medicine->image,
                        'unit' => $dbItem->medicine->unit,
                        'requires_prescription' => (bool) $dbItem->medicine->requires_prescription,
                        'slug' => $dbItem->medicine->slug,
                        'stock' => $dbItem->medicine->stock,
                    ];
                }
            }
            return $cart;
        }

        $cart = session()->get('cart', []);
        foreach ($cart as $id => &$item) {
            $medicine = Medicine::find($id);
            $item['stock'] = $medicine ? $medicine->stock : 0;
        }
        unset($item);
        return $cart;
    }

    // View cart
    public function index()
    {
        $categories = Category::all();
        $cart = $this->getCartItems();
        
        // Calculate totals
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        return view('cart', compact('cart', 'categories', 'subtotal'));
    }

    // Add item to cart
    public function add(Request $request)
    {
        $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $id = $request->medicine_id;
        $quantity = (int) $request->quantity;
        $medicine = Medicine::findOrFail($id);

        if (!$medicine->is_active) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Obat sedang tidak aktif.'], 400);
            }
            return back()->with('error', 'Obat sedang tidak aktif.');
        }

        if (auth()->check()) {
            $cartItem = CartItem::where('user_id', auth()->id())
                ->where('medicine_id', $id)
                ->first();
            
            $currentQty = $cartItem ? $cartItem->quantity : 0;
            $newQty = $currentQty + $quantity;

            if ($newQty > $medicine->stock) {
                $availableToAdd = $medicine->stock - $currentQty;
                if ($availableToAdd > 0) {
                    $newQty = $medicine->stock;
                    if ($cartItem) {
                        $cartItem->update(['quantity' => $newQty]);
                    } else {
                        CartItem::create([
                            'user_id' => auth()->id(),
                            'medicine_id' => $id,
                            'quantity' => $newQty,
                        ]);
                    }
                    $msg = "Berhasil menambahkan obat. Jumlah di keranjang Anda disesuaikan ke batas maksimal stok ({$medicine->stock} Pcs) karena sebelumnya Anda sudah memiliki {$currentQty} item di keranjang.";
                    if ($request->wantsJson()) {
                        return response()->json(['success' => true, 'message' => $msg]);
                    }
                    return back()->with('success', $msg);
                } else {
                    $msg = "Stok tidak mencukupi. Anda sudah memiliki {$currentQty} item di keranjang (batas maksimal stok).";
                    if ($request->wantsJson()) {
                        return response()->json(['success' => false, 'message' => $msg], 400);
                    }
                    return back()->with('error', $msg);
                }
            }

            if ($cartItem) {
                $cartItem->update(['quantity' => $newQty]);
            } else {
                CartItem::create([
                    'user_id' => auth()->id(),
                    'medicine_id' => $id,
                    'quantity' => $quantity,
                ]);
            }
        } else {
            $cart = session()->get('cart', []);
            $currentQty = isset($cart[$id]) ? $cart[$id]['quantity'] : 0;
            $newQty = $currentQty + $quantity;

            if ($newQty > $medicine->stock) {
                $availableToAdd = $medicine->stock - $currentQty;
                if ($availableToAdd > 0) {
                    $newQty = $medicine->stock;
                    if (isset($cart[$id])) {
                        $cart[$id]['quantity'] = $newQty;
                    } else {
                        $cart[$id] = [
                            'id' => $medicine->id,
                            'name' => $medicine->name,
                            'price' => (float) $medicine->price,
                            'quantity' => $newQty,
                            'image' => $medicine->image,
                            'unit' => $medicine->unit,
                            'requires_prescription' => (bool) $medicine->requires_prescription,
                            'slug' => $medicine->slug,
                        ];
                    }
                    session()->put('cart', $cart);
                    $msg = "Berhasil menambahkan obat. Jumlah di keranjang Anda disesuaikan ke batas maksimal stok ({$medicine->stock} Pcs) karena sebelumnya Anda sudah memiliki {$currentQty} item di keranjang.";
                    if ($request->wantsJson()) {
                        return response()->json(['success' => true, 'message' => $msg]);
                    }
                    return back()->with('success', $msg);
                } else {
                    $msg = "Stok tidak mencukupi. Anda sudah memiliki {$currentQty} item di keranjang (batas maksimal stok).";
                    if ($request->wantsJson()) {
                        return response()->json(['success' => false, 'message' => $msg], 400);
                    }
                    return back()->with('error', $msg);
                }
            }

            if (isset($cart[$id])) {
                $cart[$id]['quantity'] = $newQty;
            } else {
                $cart[$id] = [
                    'id' => $medicine->id,
                    'name' => $medicine->name,
                    'price' => (float) $medicine->price,
                    'quantity' => $quantity,
                    'image' => $medicine->image,
                    'unit' => $medicine->unit,
                    'requires_prescription' => (bool) $medicine->requires_prescription,
                    'slug' => $medicine->slug,
                ];
            }
            session()->put('cart', $cart);
        }

        $cartCount = $this->getCartCount();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Obat berhasil ditambahkan ke keranjang belanja.',
                'cart_count' => $cartCount
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Obat berhasil ditambahkan ke keranjang!');
    }

    // Update cart item quantity
    public function update(Request $request)
    {
        $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $id = $request->medicine_id;
        $quantity = (int) $request->quantity;
        $medicine = Medicine::findOrFail($id);

        $adjusted = false;
        if ($quantity > $medicine->stock) {
            $quantity = $medicine->stock;
            $adjusted = true;
        }

        if (auth()->check()) {
            $cartItem = CartItem::where('user_id', auth()->id())
                ->where('medicine_id', $id)
                ->first();
            
            if (!$cartItem) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Obat tidak ditemukan di keranjang.'], 404);
                }
                return redirect()->route('cart.index')->with('error', 'Obat tidak ditemukan di keranjang.');
            }
            
            $cartItem->update(['quantity' => $quantity]);
        } else {
            $cart = session()->get('cart', []);
            if (!isset($cart[$id])) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Obat tidak ditemukan di keranjang.'], 404);
                }
                return redirect()->route('cart.index')->with('error', 'Obat tidak ditemukan di keranjang.');
            }
            $cart[$id]['quantity'] = $quantity;
            session()->put('cart', $cart);
        }

        // Calculate totals for AJAX response
        $cart = $this->getCartItems();
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $adjusted ? "Kuantitas disesuaikan ke batas stok maksimum ({$medicine->stock})." : 'Keranjang belanja berhasil diperbarui.',
                'quantity' => $quantity,
                'item_subtotal' => number_format($medicine->price * $quantity, 0, ',', '.'),
                'cart_subtotal' => number_format($subtotal, 0, ',', '.'),
                'cart_total' => number_format($subtotal, 0, ',', '.'), // Shipping added later
                'cart_count' => $this->getCartCount()
            ]);
        }

        $msg = $adjusted ? "Kuantitas disesuaikan ke batas stok maksimum ({$medicine->stock})." : 'Keranjang belanja berhasil diperbarui.';
        return redirect()->route('cart.index')->with('success', $msg);
    }

    // Remove item from cart
    public function remove(Request $request)
    {
        $request->validate([
            'medicine_id' => 'required'
        ]);

        $id = $request->medicine_id;

        if (auth()->check()) {
            CartItem::where('user_id', auth()->id())
                ->where('medicine_id', $id)
                ->delete();
        } else {
            $cart = session()->get('cart', []);
            if (isset($cart[$id])) {
                unset($cart[$id]);
                session()->put('cart', $cart);
            }
        }

        // Calculate totals
        $cart = $this->getCartItems();
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus dari keranjang belanja.',
                'cart_subtotal' => number_format($subtotal, 0, ',', '.'),
                'cart_total' => number_format($subtotal, 0, ',', '.'),
                'cart_count' => $this->getCartCount()
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Item berhasil dihapus dari keranjang belanja.');
    }

    // Helper for cart count
    private function getCartCount()
    {
        if (auth()->check()) {
            return (int) CartItem::where('user_id', auth()->id())->sum('quantity');
        }

        $cart = session()->get('cart', []);
        $count = 0;
        foreach ($cart as $item) {
            $count += $item['quantity'];
        }
        return $count;
    }
}
