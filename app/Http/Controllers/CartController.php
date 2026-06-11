<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Medicine;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Tampilkan halaman keranjang belanja.
     */
    public function index()
    {
        $categories = Category::all();
        $cart = session()->get('cart', []);
        
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        // Estimasi biaya pengiriman (flat rate Rp 15.000, gratis jika belanja > Rp 150.000)
        $shipping = $subtotal > 0 ? ($subtotal > 150000 ? 0 : 15000) : 0;
        $total = $subtotal + $shipping;

        return view('cart', compact('categories', 'cart', 'subtotal', 'shipping', 'total'));
    }

    /**
     * Tambahkan obat ke dalam keranjang.
     */
    public function add(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);
        
        // Cek keaktifan produk
        if (!$medicine->is_active) {
            return redirect()->back()->with('error', 'Produk ini sedang tidak aktif.');
        }

        $quantity = intval($request->input('quantity', 1));
        if ($quantity <= 0) {
            return redirect()->back()->with('error', 'Kuantitas tidak valid.');
        }

        $cart = session()->get('cart', []);

        // Cek jika produk sudah ada di keranjang
        if (isset($cart[$id])) {
            $newQuantity = $cart[$id]['quantity'] + $quantity;
            
            // Validasi stok
            if ($newQuantity > $medicine->stock) {
                return redirect()->back()->with('error', "Stok tidak mencukupi. Sisa stok: {$medicine->stock}.");
            }
            
            $cart[$id]['quantity'] = $newQuantity;
        } else {
            // Validasi stok awal
            if ($quantity > $medicine->stock) {
                return redirect()->back()->with('error', "Stok tidak mencukupi. Sisa stok: {$medicine->stock}.");
            }

            // Simpan informasi produk ke session cart
            $cart[$id] = [
                'id' => $medicine->id,
                'name' => $medicine->name,
                'price' => $medicine->price,
                'quantity' => $quantity,
                'image' => $medicine->image,
                'unit' => $medicine->unit,
                'stock' => $medicine->stock,
                'requires_prescription' => $medicine->requires_prescription,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', "{$medicine->name} berhasil ditambahkan ke keranjang!");
    }

    /**
     * Perbarui kuantitas obat di dalam keranjang.
     */
    public function update(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);
        $quantity = intval($request->input('quantity'));
        $cart = session()->get('cart', []);

        if (!isset($cart[$id])) {
            return redirect()->route('cart.index')->with('error', 'Produk tidak ditemukan di keranjang.');
        }

        if ($quantity <= 0) {
            // Jika kuantitas 0 atau negatif, hapus item dari keranjang
            unset($cart[$id]);
            session()->put('cart', $cart);
            return redirect()->route('cart.index')->with('success', 'Item berhasil dihapus dari keranjang.');
        }

        // Validasi stok
        if ($quantity > $medicine->stock) {
            return redirect()->route('cart.index')->with('error', "Stok tidak mencukupi untuk memperbarui jumlah. Sisa stok: {$medicine->stock}.");
        }

        // Update kuantitas
        $cart[$id]['quantity'] = $quantity;
        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Keranjang belanja berhasil diperbarui.');
    }

    /**
     * Hapus obat dari keranjang.
     */
    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $name = $cart[$id]['name'];
            unset($cart[$id]);
            session()->put('cart', $cart);
            return redirect()->route('cart.index')->with('success', "{$name} berhasil dihapus dari keranjang.");
        }

        return redirect()->route('cart.index')->with('error', 'Produk tidak ditemukan di keranjang.');
    }

    /**
     * Kosongkan seluruh isi keranjang.
     */
    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('cart.index')->with('success', 'Keranjang belanja berhasil dikosongkan.');
    }
}
