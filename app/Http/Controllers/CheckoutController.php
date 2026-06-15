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
        $cart = (new CartController())->getCartItems();

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        $subtotal = 0;

        foreach ($cart as $item) {
            if ($item['stock'] == 0) {
                return redirect()->route('cart.index')->with('error', "Obat '{$item['name']}' sedang habis (Out of Stock). Mohon hapus dari keranjang sebelum melanjutkan.");
            }
            if ($item['quantity'] > $item['stock']) {
                return redirect()->route('cart.index')->with('error', "Stok obat '{$item['name']}' tidak mencukupi (Tersedia: {$item['stock']}). Mohon kurangi jumlahnya sebelum melanjutkan.");
            }

            $subtotal += $item['price'] * $item['quantity'];
        }

        $user = auth()->user();

        return view('checkout', compact('cart', 'categories', 'subtotal', 'user'));
    }

    // Place order
    public function store(Request $request)
    {
        $cart = (new CartController())->getCartItems();

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        // Dynamic validation
        $validationRules = [
            'order_type' => 'required|in:pickup,delivery',
            'payment_method' => 'required|in:cash,transfer,bpjs,qris',
            'shipping_address' => 'required_if:order_type,delivery|nullable|string',
            'location_details' => 'required_if:order_type,delivery|nullable|string|max:255',
            'delivery_latitude' => 'required_if:order_type,delivery|nullable|numeric',
            'delivery_longitude' => 'required_if:order_type,delivery|nullable|numeric',
            'delivery_distance' => 'required_if:order_type,delivery|nullable|numeric',
            'notes' => 'nullable|string|max:255',
            'payment_proof' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        $request->validate($validationRules);

        // Additional validation for Cash payment + Delivery
        if ($request->payment_method === 'cash' && $request->order_type === 'delivery') {
            return back()->withInput()->with('error', 'Pembayaran tunai hanya tersedia untuk metode pengambilan di apotek (Pickup).');
        }

        // Calculate pricing
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $shippingCost = 0;
        $distance = null;
        $latitude = null;
        $longitude = null;

        if ($request->order_type === 'delivery') {
            $latitude = (double) $request->delivery_latitude;
            $longitude = (double) $request->delivery_longitude;
            
            $routeData = $this->getDistanceAndShipping($latitude, $longitude);
            $distance = $routeData['distance'];
            
            if ($distance > 50) {
                return back()->withInput()->with('error', 'Gagal memproses pesanan: Jarak pengiriman terlalu jauh (Maksimum 50 km).');
            }
            
            $shippingCost = $routeData['shipping_cost'];
        }

        $totalAmount = $subtotal + $shippingCost;

        // Perform inside a transaction for safety
        try {
            $order = DB::transaction(function () use ($request, $cart, $subtotal, $shippingCost, $totalAmount, $latitude, $longitude, $distance) {
                
                // 1. Generate unique order number: ORD-YYYYMMDD-XXXX
                $datePart = date('Ymd');
                $randomPart = strtoupper(Str::random(5));
                $orderNumber = "ORD-{$datePart}-{$randomPart}";

                // 2. Create the Order
                $shippingAddress = $request->shipping_address;
                if ($request->order_type === 'delivery' && $request->filled('location_details')) {
                    $shippingAddress .= "\nDetail Lokasi: " . $request->location_details;
                }

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
                    'shipping_address' => $request->order_type === 'delivery' ? $shippingAddress : null,
                    'delivery_latitude' => $latitude,
                    'delivery_longitude' => $longitude,
                    'delivery_distance' => $distance,
                    'notes' => $request->notes,
                ]);

                // 3. Process payment proof if uploaded (Public storage)
                if ($request->hasFile('payment_proof')) {
                    $imagePath = $request->file('payment_proof')->store('payment_proofs', 'public');
                    $order->update([
                        'payment_proof' => '/storage/' . $imagePath
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

            // Clear database cart items
            \App\Models\CartItem::where('user_id', auth()->id())->delete();

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

    // Ajax action to calculate routing distance and cost
    public function calculateDistance(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $latitude = (double) $request->latitude;
        $longitude = (double) $request->longitude;

        try {
            $routeData = $this->getDistanceAndShipping($latitude, $longitude);

            return response()->json([
                'status' => 'success',
                'distance' => round($routeData['distance'], 2),
                'shipping_cost' => $routeData['shipping_cost'],
                'formatted_shipping_cost' => 'Rp ' . number_format($routeData['shipping_cost'], 0, ',', '.'),
                'geometry' => $routeData['geometry']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Helper method to fetch road routing from OSRM or fallback to Haversine
    private function getDistanceAndShipping($latitude, $longitude)
    {
        // Pharmacy coordinates (Andalas, Padang)
        $pharmacyLat = -0.937722;
        $pharmacyLng = 100.3878982;

        // Round to 4 decimal places for caching key (approx 11m accuracy)
        $latRounded = round($latitude, 4);
        $lngRounded = round($longitude, 4);
        $cacheKey = "route_{$latRounded}_{$lngRounded}";

        return cache()->remember($cacheKey, 3600, function () use ($pharmacyLat, $pharmacyLng, $latitude, $longitude) {
            $distance = null;
            $geometry = null;

            // OSRM driving route with full geometry
            $osrmUrl = "https://router.project-osrm.org/route/v1/driving/{$pharmacyLng},{$pharmacyLat};{$longitude},{$latitude}?overview=full&geometries=geojson";
            $options = [
                'http' => [
                    'header' => "User-Agent: ApotekNaufalApp/1.0\r\n",
                    'timeout' => 3 // 3 seconds timeout
                ]
            ];
            $context = stream_context_create($options);
            $osrmResponse = @file_get_contents($osrmUrl, false, $context);
            
            if ($osrmResponse) {
                $osrmData = json_decode($osrmResponse, true);
                if (isset($osrmData['routes'][0]['distance'])) {
                    $distance = $osrmData['routes'][0]['distance'] / 1000; // convert to km
                    $geometry = $osrmData['routes'][0]['geometry'] ?? null;
                }
            }

            // Fallback to Haversine straight-line if OSRM failed
            if ($distance === null) {
                $earthRadius = 6371; // km
                $dLat = deg2rad($latitude - $pharmacyLat);
                $dLng = deg2rad($longitude - $pharmacyLng);
                $a = sin($dLat/2) * sin($dLat/2) +
                     cos(deg2rad($pharmacyLat)) * cos(deg2rad($latitude)) *
                     sin($dLng/2) * sin($dLng/2);
                $c = 2 * atan2(sqrt($a), sqrt(1-$a));
                $distance = $earthRadius * $c;

                // Create straight-line geojson line geometry
                $geometry = [
                    'type' => 'LineString',
                    'coordinates' => [
                        [$pharmacyLng, $pharmacyLat],
                        [$longitude, $latitude]
                    ]
                ];
            }

            // Rp 2.500 per km, minimal Rp 10.000
            $calculatedFee = ceil($distance) * 2500;
            $shippingCost = max($calculatedFee, 10000);

            return [
                'distance' => $distance,
                'shipping_cost' => $shippingCost,
                'geometry' => $geometry
            ];
        });
    }
}
