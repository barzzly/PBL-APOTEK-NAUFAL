<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Medicine;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Services\GeminiService;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    public function dashboard()
    {
        $categoriesCount = Category::count();
        $medicinesCount  = Medicine::count();

        // Order stats
        $totalOrders     = Order::count();
        $ordersPending   = Order::where('status', 'pending')->count();
        $ordersToday     = Order::whereDate('created_at', today())->count();

        // Revenue
        $revenueToday    = Order::whereDate('created_at', today())
                                ->where('payment_status', 'paid')
                                ->sum('total_amount');
        $revenueMonth    = Order::whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->where('payment_status', 'paid')
                                ->sum('total_amount');

        // Low stock medicines (stock < 30)
        $lowStockMedicines = Medicine::where('stock', '<', 30)
                                     ->orderBy('stock')
                                     ->get();
        $lowStockCount = $lowStockMedicines->count();

        // Recent orders
        $recentOrders = Order::with('user')
                             ->latest()
                             ->limit(6)
                             ->get();

        return view('admin.dashboard', compact(
            'categoriesCount', 'medicinesCount',
            'totalOrders', 'ordersPending', 'ordersToday',
            'revenueToday', 'revenueMonth',
            'lowStockMedicines', 'lowStockCount', 'recentOrders'
        ));
    }

    // --- Categories ---
    public function categories(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        $categories = Category::latest()->paginate($perPage)->withQueryString();
        return view('admin.categories', compact('categories', 'perPage'));
    }

    public function createCategory()
    {
        return view('admin.categories_create');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);
        
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $this->convertToWebp($request->file('image'), 'categories');
        }

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $imagePath ? '/storage/' . $imagePath : null,
        ]);

        return redirect()->route('admin.categories')->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function editCategory($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories_edit', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);
        
        if ($request->hasFile('image')) {
            $imagePath = $this->convertToWebp($request->file('image'), 'categories');
            $category->image = '/storage/' . $imagePath;
        }

        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->save();

        return redirect()->route('admin.categories')->with('success', 'Kategori berhasil diperbarui!');
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return back()->with('success', 'Kategori berhasil dihapus!');
    }

    // --- Medicines ---
    public function medicines(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSortBy = ['name', 'category_name', 'price', 'stock', 'created_at'];
        if (!in_array($sortBy, $allowedSortBy)) {
            $sortBy = 'created_at';
        }
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        $query = Medicine::with('category');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('medicines.name', 'like', '%' . $search . '%')
                  ->orWhereHas('category', function($catQuery) use ($search) {
                      $catQuery->where('categories.name', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($sortBy === 'category_name') {
            $query->leftJoin('categories', 'medicines.category_id', '=', 'categories.id')
                  ->select('medicines.*')
                  ->orderBy('categories.name', $sortOrder);
        } else {
            $query->orderBy('medicines.' . $sortBy, $sortOrder);
        }

        $medicines = $query->paginate($perPage)->withQueryString();
        return view('admin.medicines', compact('medicines', 'perPage', 'sortBy', 'sortOrder'));
    }

    public function createMedicine()
    {
        $categories = Category::all();
        return view('admin.medicines_create', compact('categories'));
    }

    public function storeMedicine(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'description' => 'nullable|string'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $this->convertToWebp($request->file('image'), 'medicines');
        }

        $stock = $request->stock;
        $unit = $request->unit;

        if (strtolower($unit) === 'kardus') {
            $stock = $stock * 24;
            $unit = 'box';
        }

        Medicine::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'price' => $request->price,
            'stock' => $stock,
            'unit' => $unit,
            'image' => $imagePath ? '/storage/' . $imagePath : null,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.medicines')->with('success', 'Obat berhasil ditambahkan!');
    }

    public function editMedicine($id)
    {
        $medicine = Medicine::findOrFail($id);
        $categories = Category::all();
        return view('admin.medicines_edit', compact('medicine', 'categories'));
    }

    public function updateMedicine(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock_action' => 'required|in:add,subtract,set',
            'stock_value' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'description' => 'nullable|string'
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $this->convertToWebp($request->file('image'), 'medicines');
            $medicine->image = '/storage/' . $imagePath;
        }

        $stockAction = $request->stock_action;
        $stockValue = (int) $request->stock_value;
        $unit = $request->unit;

        if (strtolower($unit) === 'kardus') {
            $stockValue = $stockValue * 24;
            $unit = 'box';
        }

        $medicine->category_id = $request->category_id;
        $medicine->name = $request->name;
        $medicine->slug = Str::slug($request->name);
        $medicine->price = $request->price;

        if ($stockAction === 'add') {
            $medicine->stock = $medicine->stock + $stockValue;
        } elseif ($stockAction === 'subtract') {
            $medicine->stock = max(0, $medicine->stock - $stockValue);
        } elseif ($stockAction === 'set') {
            $medicine->stock = $stockValue;
        }

        $medicine->unit = $unit;
        $medicine->description = $request->description;
        $medicine->save();

        return redirect()->route('admin.medicines')->with('success', 'Obat berhasil diperbarui!');
    }

    public function deleteMedicine($id)
    {
        $medicine = Medicine::findOrFail($id);
        $medicine->delete();
        return back()->with('success', 'Obat berhasil dihapus!');
    }

    public function generateDescription(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $category = null;
        if ($request->category_id) {
            $category = Category::find($request->category_id);
        }

        try {
            $geminiService = new GeminiService();
            $description = $geminiService->generateDescription($request->name, $category ? $category->name : null);
            return response()->json([
                'success' => true,
                'description' => $description
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // --- Laporan Penjualan ---
    public function laporanPenjualan(Request $request)
    {
        // Determine date range
        $period  = $request->get('period', '30');
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');

        if ($period === 'custom' && $dateFrom && $dateTo) {
            $from = Carbon::parse($dateFrom)->startOfDay();
            $to   = Carbon::parse($dateTo)->endOfDay();
        } else {
            $days = (int) $period;
            $from = Carbon::now()->subDays($days - 1)->startOfDay();
            $to   = Carbon::now()->endOfDay();
        }

        $statusFilter  = $request->get('status', 'all');
        $paymentFilter = $request->get('payment_method', 'all');

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSortBy = ['order_number', 'customer_name', 'created_at', 'order_type', 'payment_method', 'status', 'payment_status', 'total_amount'];
        if (!in_array($sortBy, $allowedSortBy)) {
            $sortBy = 'created_at';
        }
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        // Base query builder
        $baseQuery = Order::with('user')
            ->whereBetween('orders.created_at', [$from, $to]);

        if ($statusFilter !== 'all') {
            $baseQuery->where('orders.status', $statusFilter);
        }
        if ($paymentFilter !== 'all') {
            $baseQuery->where('orders.payment_method', $paymentFilter);
        }

        // Summary cards
        $totalPendapatan = (clone $baseQuery)->where('orders.payment_status', 'paid')->sum('orders.total_amount');
        $totalOrder      = (clone $baseQuery)->count();
        $orderSelesai    = (clone $baseQuery)->where('orders.status', 'delivered')->count();
        $orderDibatalkan = (clone $baseQuery)->where('orders.status', 'cancelled')->count();

        // Orders list with pagination (restricted to delivered and cancelled)
        $perPage = (int) $request->get('per_page', 10);
        $listQuery = (clone $baseQuery);
        if ($statusFilter === 'all') {
            $listQuery->whereIn('orders.status', ['delivered', 'cancelled']);
        } else {
            $listQuery->where('orders.status', $statusFilter);
        }

        if ($sortBy === 'customer_name') {
            $listQuery->leftJoin('users', 'orders.user_id', '=', 'users.id')
                      ->select('orders.*')
                      ->orderBy('users.name', $sortOrder);
        } else {
            $listQuery->orderBy('orders.' . $sortBy, $sortOrder);
        }

        $orders = $listQuery->paginate($perPage)->withQueryString();

        // Top selling medicines in the date range
        $topMedicines = OrderItem::select(
                'medicine_id',
                'medicine_name',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as total_revenue')
            )
            ->whereHas('order', function ($q) use ($from, $to) {
                $q->whereBetween('created_at', [$from, $to])
                  ->where('payment_status', 'paid');
            })
            ->with('medicine.category')
            ->groupBy('medicine_id', 'medicine_name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        return view('admin.laporan_penjualan', compact(
            'totalPendapatan', 'totalOrder', 'orderSelesai', 'orderDibatalkan',
            'orders', 'topMedicines',
            'period', 'dateFrom', 'dateTo', 'statusFilter', 'paymentFilter',
            'from', 'to', 'perPage', 'sortBy', 'sortOrder'
        ));
    }

    public function exportLaporanPenjualan(Request $request)
    {
        $period = $request->get('period', '30');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        if ($period === 'custom' && $dateFrom && $dateTo) {
            $from = Carbon::parse($dateFrom)->startOfDay();
            $to = Carbon::parse($dateTo)->endOfDay();
        } else {
            $days = max(1, (int) $period);
            $from = Carbon::now()->subDays($days - 1)->startOfDay();
            $to = Carbon::now()->endOfDay();
        }

        $statusFilter = $request->get('status', 'all');
        $paymentFilter = $request->get('payment_method', 'all');

        $query = Order::with('user')
            ->whereBetween('orders.created_at', [$from, $to]);

        if ($statusFilter === 'all') {
            $query->whereIn('orders.status', ['delivered', 'cancelled']);
        } else {
            $query->where('orders.status', $statusFilter);
        }

        if ($paymentFilter !== 'all') {
            $query->where('orders.payment_method', $paymentFilter);
        }

        $orders = $query->orderBy('orders.created_at', 'desc')->get();
        $paidRevenue = $orders->where('payment_status', 'paid')->sum('total_amount');
        $filename = 'laporan-penjualan-' . $from->format('Ymd') . '-' . $to->format('Ymd') . '.xls';

        return response()->streamDownload(function () use ($orders, $from, $to, $statusFilter, $paymentFilter, $paidRevenue) {
            echo '<html><head><meta charset="UTF-8"><style>';
            echo 'table{border-collapse:collapse;font-family:Arial,sans-serif;font-size:12px}';
            echo 'th{background:#16704A;color:#fff;font-weight:bold}th,td{border:1px solid #DDE7E2;padding:6px 8px}';
            echo '.title{font-size:18px;font-weight:bold;text-align:center}.meta{text-align:center;color:#333}.money{mso-number-format:"\\0022Rp\\0022 #,##0";text-align:right}.bold{font-weight:bold}';
            echo '</style></head><body>';
            echo '<table>';
            echo '<tr><td colspan="14" class="title">Laporan Penjualan Apotek Naufal</td></tr>';
            echo '<tr><td colspan="14" class="meta">Periode: ' . e($from->format('d/m/Y')) . ' - ' . e($to->format('d/m/Y')) . '</td></tr>';
            echo '<tr><td colspan="14" class="meta">Status: ' . e($statusFilter === 'all' ? 'Semua' : $statusFilter) . ' | Metode Bayar: ' . e($paymentFilter === 'all' ? 'Semua' : strtoupper($paymentFilter)) . '</td></tr>';
            echo '<tr></tr>';
            echo '<tr>';
            foreach (['No', 'Tanggal', 'No Order', 'Pelanggan', 'Email', 'Telepon', 'Jenis Order', 'Status Order', 'Metode Bayar', 'Status Bayar', 'Subtotal', 'Ongkir', 'Diskon', 'Total'] as $header) {
                echo '<th>' . e($header) . '</th>';
            }
            echo '</tr>';

            foreach ($orders as $index => $order) {
                $values = [
                    $index + 1,
                    optional($order->created_at)->format('d/m/Y H:i'),
                    $order->order_number,
                    optional($order->user)->name ?? '-',
                    optional($order->user)->email ?? '-',
                    optional($order->user)->phone ?? '-',
                    $order->order_type === 'delivery' ? 'Delivery' : 'Pickup',
                    $order->status_label,
                    $order->payment_method_label,
                    $order->payment_status_label,
                    (float) $order->subtotal,
                    (float) $order->shipping_cost,
                    (float) $order->discount,
                    (float) $order->total_amount,
                ];

                echo '<tr>';
                foreach ($values as $columnIndex => $value) {
                    $class = $columnIndex >= 10 ? ' class="money"' : '';
                    echo '<td' . $class . '>' . e((string) $value) . '</td>';
                }
                echo '</tr>';
            }

            echo '<tr></tr>';
            echo '<tr><td colspan="13" class="bold">Total Pendapatan Lunas</td><td class="money bold">' . e((string) (float) $paidRevenue) . '</td></tr>';
            echo '<tr><td colspan="13" class="bold">Total Order</td><td class="bold">' . e((string) $orders->count()) . '</td></tr>';
            echo '</table></body></html>';
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
        ]);
    }

    public function exportPdfLaporanPenjualan(Request $request)
    {
        $period = $request->get('period', '30');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        if ($period === 'custom' && $dateFrom && $dateTo) {
            $from = Carbon::parse($dateFrom)->startOfDay();
            $to = Carbon::parse($dateTo)->endOfDay();
        } else {
            $days = max(1, (int) $period);
            $from = Carbon::now()->subDays($days - 1)->startOfDay();
            $to = Carbon::now()->endOfDay();
        }

        $statusFilter = $request->get('status', 'all');
        $paymentFilter = $request->get('payment_method', 'all');

        $query = Order::with(['user', 'items.medicine'])
            ->whereBetween('orders.created_at', [$from, $to]);

        if ($statusFilter === 'all') {
            $query->whereIn('orders.status', ['delivered', 'cancelled']);
        } else {
            $query->where('orders.status', $statusFilter);
        }

        if ($paymentFilter !== 'all') {
            $query->where('orders.payment_method', $paymentFilter);
        }

        $orders = $query->orderBy('orders.created_at', 'desc')->get();

        $totalPendapatan = $orders->where('payment_status', 'paid')->sum('total_amount');
        $totalOrder = $orders->count();
        $orderSelesai = $orders->where('status', 'delivered')->count();
        $orderDibatalkan = $orders->where('status', 'cancelled')->count();

        $pdf = Pdf::loadView('admin.pdf_laporan_penjualan', compact(
            'orders', 'totalPendapatan', 'totalOrder', 'orderSelesai', 'orderDibatalkan',
            'period', 'dateFrom', 'dateTo', 'statusFilter', 'paymentFilter', 'from', 'to'
        ))->setPaper('a4', 'landscape');

        $filename = 'laporan-penjualan-' . $from->format('Ymd') . '-' . $to->format('Ymd') . '.pdf';
        return $pdf->download($filename);
    }

    public function laporanChartData(Request $request)
    {
        $period  = $request->get('period', '30');
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');

        if ($period === 'custom' && $dateFrom && $dateTo) {
            $from = Carbon::parse($dateFrom)->startOfDay();
            $to   = Carbon::parse($dateTo)->endOfDay();
        } else {
            $days = (int) $period;
            $from = Carbon::now()->subDays($days - 1)->startOfDay();
            $to   = Carbon::now()->endOfDay();
        }

        // Daily revenue (paid orders only)
        $revenueData = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Daily order count (all statuses)
        $orderCountData = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween('created_at', [$from, $to])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Build full date range labels
        $labels   = [];
        $revenues = [];
        $counts   = [];

        $current = $from->copy();
        while ($current->lte($to)) {
            $dateKey   = $current->toDateString();
            $labels[]  = $current->isoFormat('DD MMM');
            $revenues[] = $revenueData->has($dateKey) ? (float) $revenueData[$dateKey]->revenue : 0;
            $counts[]  = $orderCountData->has($dateKey) ? (int) $orderCountData[$dateKey]->total : 0;
            $current->addDay();
        }

        return response()->json(compact('labels', 'revenues', 'counts'));
    }

    // --- Order Management ---
    public function orders(Request $request)
    {
        $status = $request->get('status', 'all');
        $perPage = (int) $request->get('per_page', 10);
        
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSortBy = ['order_number', 'customer_name', 'created_at', 'order_type', 'payment_status', 'total_amount', 'status'];
        if (!in_array($sortBy, $allowedSortBy)) {
            $sortBy = 'created_at';
        }
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        $query = Order::with('user')
            ->whereIn('orders.status', ['pending', 'confirmed', 'ready_for_pickup', 'shipped']);

        if ($sortBy === 'customer_name') {
            $query->leftJoin('users', 'orders.user_id', '=', 'users.id')
                  ->select('orders.*')
                  ->orderBy('users.name', $sortOrder);
        } else {
            $query->orderBy('orders.' . $sortBy, $sortOrder);
        }

        $orders = $query->paginate($perPage)->withQueryString();
        return view('admin.orders', compact('orders', 'status', 'perPage', 'sortBy', 'sortOrder'));
    }

    public function showOrder($id)
    {
        $order = Order::with(['items.medicine', 'user'])->findOrFail($id);
        
        // Fetch prescription if exists
        $prescription = \App\Models\Prescription::where('order_id', $order->id)->first();
        
        return view('admin.order_show', compact('order', 'prescription'));
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,confirmed,ready_for_pickup,shipped,delivered,cancelled',
            'payment_status' => 'required|in:unpaid,paid,refunded',
            'pharmacist_note' => 'nullable|string|max:255',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        // If order gets cancelled, restock medicines
        if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
            foreach ($order->items as $item) {
                if ($item->medicine) {
                    $item->medicine->increment('stock', $item->quantity);
                }
            }
        }
        
        // If order was cancelled but gets un-cancelled (restored), reduce stock again
        if ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
            foreach ($order->items as $item) {
                if ($item->medicine) {
                    $item->medicine->decrement('stock', $item->quantity);
                }
            }
        }

        $updateData = [
            'status' => $newStatus,
            'payment_status' => $request->payment_status,
            'pharmacist_note' => $request->pharmacist_note,
        ];

        if ($request->payment_status === 'paid' && $order->payment_status !== 'paid') {
            $updateData['paid_at'] = now();
        }

        $order->update($updateData);

        // Trigger WhatsApp status notification if status changed
        if ($oldStatus !== $newStatus) {
            try {
                \App\Services\WhatsAppNotificationService::sendOrderNotification($order, $newStatus);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send status update WA notification: " . $e->getMessage());
            }
        }

        // Update prescription status if exists
        $prescription = \App\Models\Prescription::where('order_id', $order->id)->first();
        if ($prescription) {
            if ($newStatus === 'cancelled') {
                $prescription->update(['status' => 'rejected']);
            } elseif ($newStatus === 'delivered') {
                $prescription->update(['status' => 'completed']);
            } elseif (in_array($newStatus, ['confirmed', 'ready_for_pickup', 'shipped'])) {
                $prescription->update([
                    'status' => 'verified',
                    'verified_by' => auth()->id(),
                    'verified_at' => now(),
                ]);
            }
        }

        return redirect()->route('admin.orders.show', $order->id)->with('success', 'Status pesanan berhasil diperbarui!');
    }

    public function fetchNotifications()
    {
        $notificationService = new \App\Services\NotificationService();
        $notifications = $notificationService->getNotifications();
        
        return response()->json([
            'notifications' => $notifications,
            'count' => $notifications->count()
        ]);
    }

    /**
     * Convert uploaded image to WebP format.
     */
    private function convertToWebp($file, $folder)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $newFilename = Str::slug($filename) . '_' . time() . '.webp';
        
        $tempPath = $file->getRealPath();
        
        // Exif image type detection
        $type = @exif_imagetype($tempPath);
        $image = null;
        
        if ($type === IMAGETYPE_PNG || $extension === 'png') {
            $image = @imagecreatefrompng($tempPath);
        } elseif ($type === IMAGETYPE_JPEG || in_array($extension, ['jpg', 'jpeg'])) {
            $image = @imagecreatefromjpeg($tempPath);
        } elseif ($type === IMAGETYPE_GIF || $extension === 'gif') {
            $image = @imagecreatefromgif($tempPath);
        } elseif ($type === IMAGETYPE_WEBP || $extension === 'webp') {
            $image = @imagecreatefromwebp($tempPath);
        }

        if (!$image) {
            return $file->store($folder, 'public');
        }

        // Preserve transparency
        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);

        $relativeDir = 'public/' . $folder;
        $destinationDir = storage_path('app/' . $relativeDir);
        
        if (!file_exists($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }

        $destinationPath = $destinationDir . '/' . $newFilename;
        
        imagewebp($image, $destinationPath, 80);
        imagedestroy($image);

        return $folder . '/' . $newFilename;
    }
}

