<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CheckoutController extends Controller
{
    // Show checkout page
    public function index()
    {
        $categories = Category::all();
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        $subtotal = 0;
        $requiresPrescription = false;

        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
            if ($item['requires_prescription']) {
                $requiresPrescription = true;
            }
        }

        $user = auth()->user();

        return view('checkout', compact('cart', 'categories', 'subtotal', 'requiresPrescription', 'user'));
    }

    // Place order
    public function store(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        // Determine if prescription is required
        $requiresPrescription = false;
        foreach ($cart as $item) {
            if ($item['requires_prescription']) {
                $requiresPrescription = true;
            }
        }

        // Dynamic validation
        $validationRules = [
            'order_type' => 'required|in:pickup,delivery',
            'payment_method' => 'required|in:cash,transfer,bpjs,qris',
            'shipping_address' => 'required_if:order_type,delivery|nullable|string',
            'notes' => 'nullable|string|max:255',
            'payment_proof' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        if ($requiresPrescription) {
            $validationRules = array_merge($validationRules, [
                'doctor_name' => 'required|string|max:255',
                'prescription_date' => 'required|date',
                'patient_name' => 'required|string|max:255',
                'patient_age' => 'nullable|integer|min:0',
                'prescription_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        }

        $request->validate($validationRules);

        // Calculate pricing
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $shippingCost = $request->order_type === 'delivery' ? 15000 : 0;
        $totalAmount = $subtotal + $shippingCost;

        // Perform inside a transaction for safety
        try {
            $order = DB::transaction(function () use ($request, $cart, $subtotal, $shippingCost, $totalAmount, $requiresPrescription) {
                
                // 1. Generate unique order number: ORD-YYYYMMDD-XXXX
                $datePart = date('Ymd');
                $randomPart = strtoupper(Str::random(5));
                $orderNumber = "ORD-{$datePart}-{$randomPart}";

                // 2. Create the Order
                $order = Order::create([
                    'order_number' => $orderNumber,
                    'user_id' => auth()->id(),
                    'status' => 'pending',
                    'order_type' => $request->order_type,
                    'subtotal' => $subtotal,
                    'shipping_cost' => $shippingCost,
                    'discount' => 0,
                    'total_amount' => $totalAmount,
                    'payment_method' => $request->payment_method,
                    'payment_status' => 'unpaid',
                    'shipping_address' => $request->order_type === 'delivery' ? $request->shipping_address : null,
                    'notes' => $request->notes,
                ]);

                // 3. Process payment proof if uploaded (Public storage)
                if ($request->hasFile('payment_proof')) {
                    $imagePath = $request->file('payment_proof')->store('payment_proofs', 'public');
                    $order->update([
                        'payment_proof' => '/storage/' . $imagePath
                    ]);
                }

                // 4. Process prescription if required (Private storage)
                if ($requiresPrescription) {
                    $imagePath = $request->file('prescription_image')->store('prescriptions', 'local');

                    Prescription::create([
                        'user_id' => auth()->id(),
                        'order_id' => $order->id,
                        'prescription_number' => 'RX-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
                        'doctor_name' => $request->doctor_name,
                        'hospital_clinic' => $request->hospital_clinic,
                        'prescription_date' => $request->prescription_date,
                        'patient_name' => $request->patient_name,
                        'patient_age' => $request->patient_age,
                        'status' => 'pending',
                        'image' => $imagePath,
                    ]);
                }

                // 5. Create Order Items & Decrement Stock
                foreach ($cart as $item) {
                    $medicine = Medicine::findOrFail($item['id']);

                    // Verify stock availability again
                    if ($medicine->stock < $item['quantity']) {
                        throw new \Exception("Stok obat '{$medicine->name}' tidak mencukupi.");
                    }

                    OrderItem::create([
                        'order_id' => $order->id,
                        'medicine_id' => $item['id'],
                        'medicine_name' => $item['name'],
                        'medicine_unit' => $item['unit'] ?? 'pcs',
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'subtotal' => $item['price'] * $item['quantity'],
                    ]);

                    // Decrement stock
                    $medicine->decrement('stock', $item['quantity']);
                }

                return $order;
            });

            // Clear session cart
            session()->forget('cart');

            return redirect()->route('orders.success', ['order_number' => $order->order_number])
                ->with('success', 'Pesanan Anda berhasil dibuat!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }

    // Order placed successfully page
    public function success(Request $request)
    {
        $orderNumber = $request->get('order_number');
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $categories = Category::all();

        return view('orders.success', compact('order', 'categories'));
    }

    // Customer order history
    public function history()
    {
        $categories = Category::all();
        $orders = Order::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('orders.history', compact('orders', 'categories'));
    }

    // Customer order detail
    public function show($id)
    {
        $categories = Category::all();
        $order = Order::with(['items', 'user'])->where('user_id', auth()->id())->findOrFail($id);
        
        // Find prescription if exists
        $prescription = Prescription::where('order_id', $order->id)->first();

        return view('orders.show', compact('order', 'categories', 'prescription'));
    }

    // Upload payment proof after checkout
    public function uploadPaymentProof(Request $request, $id)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $order = Order::where('user_id', auth()->id())->findOrFail($id);

        if ($request->hasFile('payment_proof')) {
            $imagePath = $request->file('payment_proof')->store('payment_proofs', 'public');
            
            $order->update([
                'payment_proof' => '/storage/' . $imagePath
            ]);

            return back()->with('success', 'Bukti pembayaran berhasil diunggah! Admin akan segera memverifikasinya.');
        }

        return back()->with('error', 'Gagal mengunggah bukti pembayaran.');
    }

    // Secure view prescription file
    public function viewPrescription($filename)
    {
        $prescription = Prescription::where('image', 'prescriptions/' . $filename)->firstOrFail();

        // Admin or the user who owns the prescription can access it
        if (auth()->user()->role !== 'admin' && auth()->id() !== $prescription->user_id) {
            abort(403, 'Anda tidak memiliki akses untuk melihat berkas resep ini.');
        }

        $path = 'prescriptions/' . $filename;
        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'Berkas resep tidak ditemukan.');
        }

        return response()->file(storage_path('app/private/' . $path));
    }
}
