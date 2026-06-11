<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Category;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // View cart
    public function index()
    {
        $categories = Category::all();
        $cart = session()->get('cart', []);
        
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

        $cart = session()->get('cart', []);

        // Check stock limit
        $currentQty = isset($cart[$id]) ? $cart[$id]['quantity'] : 0;
        $newQty = $currentQty + $quantity;

        if ($newQty > $medicine->stock) {
            $msg = "Stok tidak mencukupi. Stok tersedia: {$medicine->stock}.";
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 400);
            }
            return back()->with('error', $msg);
        }

        // Add or update cart
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

        $cart = session()->get('cart', []);

        if (!isset($cart[$id])) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Obat tidak ditemukan di keranjang.'], 404);
            }
            return redirect()->route('cart.index')->with('error', 'Obat tidak ditemukan di keranjang.');
        }

        if ($quantity > $medicine->stock) {
            $msg = "Stok tidak mencukupi. Stok tersedia: {$medicine->stock}.";
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 400);
            }
            return redirect()->route('cart.index')->with('error', $msg);
        }

        $cart[$id]['quantity'] = $quantity;
        session()->put('cart', $cart);

        // Calculate totals for AJAX response
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Keranjang belanja berhasil diperbarui.',
                'item_subtotal' => number_format($cart[$id]['price'] * $quantity, 0, ',', '.'),
                'cart_subtotal' => number_format($subtotal, 0, ',', '.'),
                'cart_total' => number_format($subtotal, 0, ',', '.'), // Shipping added later
                'cart_count' => $this->getCartCount()
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Keranjang belanja berhasil diperbarui.');
    }

    // Remove item from cart
    public function remove(Request $request)
    {
        $request->validate([
            'medicine_id' => 'required'
        ]);

        $id = $request->medicine_id;
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        // Calculate totals
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
        $cart = session()->get('cart', []);
        $count = 0;
        foreach ($cart as $item) {
            $count += $item['quantity'];
        }
        return $count;
    }
}
