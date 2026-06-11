<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // Tambah Item ke Keranjang / Beli Sekarang (Simulasi session)
    public function add(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);
        $quantity = (int) $request->input('quantity', 1);

        if ($medicine->stock < $quantity) {
            return redirect()->back()->with('error', 'Stok obat tidak mencukupi.');
        }

        $cart = session()->get('cart', []);

        // Update kuantitas di session
        if (isset($cart[$id])) {
            $newQuantity = $cart[$id]['quantity'] + $quantity;
            if ($medicine->stock < $newQuantity) {
                return redirect()->back()->with('error', 'Jumlah total melebihi stok yang tersedia.');
            }
            $cart[$id]['quantity'] = $newQuantity;
        } else {
            $cart[$id] = [
                'id' => $medicine->id,
                'name' => $medicine->name,
                'price' => $medicine->price,
                'quantity' => $quantity,
                'image' => $medicine->image,
                'slug' => $medicine->slug,
            ];
        }

        session()->put('cart', $cart);

        if ($request->has('buy_now')) {
            return redirect()->back()->with('success', 'Beli Sekarang berhasil! Anda memesan: ' . $medicine->name . ' sebanyak ' . $quantity . ' pcs.');
        }

        return redirect()->back()->with('success', 'Obat "' . $medicine->name . '" (' . $quantity . ' pcs) berhasil dimasukkan ke keranjang.');
    }
}
