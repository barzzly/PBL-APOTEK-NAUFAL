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
                                     ->limit(5)
                                     ->get();
        $lowStockCount = Medicine::where('stock', '<', 30)->count();

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
        $request->validate(['name' => 'required|string|max:255']);
        
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
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
        $request->validate(['name' => 'required|string|max:255']);
        
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('medicines', 'public');
        }

        Medicine::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'price' => $request->price,
            'stock' => $request->stock,
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
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string'
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('medicines', 'public');
            $medicine->image = '/storage/' . $imagePath;
        }

        $medicine->category_id = $request->category_id;
        $medicine->name = $request->name;
        $medicine->slug = Str::slug($request->name);
        $medicine->price = $request->price;
        $medicine->stock = $request->stock;
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
            ->whereIn('orders.status', ['pending', 'confirmed', 'processing', 'ready_for_pickup', 'shipped']);

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
            'status' => 'required|in:pending,confirmed,processing,ready_for_pickup,shipped,delivered,cancelled',
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

        // Update prescription status if exists
        $prescription = \App\Models\Prescription::where('order_id', $order->id)->first();
        if ($prescription) {
            if ($newStatus === 'cancelled') {
                $prescription->update(['status' => 'rejected']);
            } elseif ($newStatus === 'delivered') {
                $prescription->update(['status' => 'completed']);
            } elseif (in_array($newStatus, ['confirmed', 'processing', 'ready_for_pickup', 'shipped'])) {
                $prescription->update([
                    'status' => 'verified',
                    'verified_by' => auth()->id(),
                    'verified_at' => now(),
                ]);
            }
        }

        return redirect()->route('admin.orders')->with('success', 'Status pesanan berhasil diperbarui!');
    }

    // --- Laporan Export ---
    public function exportLaporanPenjualan(Request $request)
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

        // Base query builder
        $baseQuery = Order::with('user')
            ->whereBetween('orders.created_at', [$from, $to]);

        if ($statusFilter !== 'all') {
            $baseQuery->where('orders.status', $statusFilter);
        }
        if ($paymentFilter !== 'all') {
            $baseQuery->where('orders.payment_method', $paymentFilter);
        }

        // Get total revenue & order stats
        $totalPendapatan = (clone $baseQuery)->where('orders.payment_status', 'paid')->sum('orders.total_amount');
        $orderSelesai    = (clone $baseQuery)->where('orders.status', 'delivered')->count();
        $orderDibatalkan = (clone $baseQuery)->where('orders.status', 'cancelled')->count();

        // Get orders (same logic as laporan)
        $listQuery = (clone $baseQuery);
        if ($statusFilter === 'all') {
            $listQuery->whereIn('orders.status', ['delivered', 'cancelled']);
        } else {
            $listQuery->where('orders.status', $statusFilter);
        }

        $orders = $listQuery->orderBy('orders.created_at', 'desc')->get();

        // Create spreadsheet using PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Title and header info
        $sheet->setCellValue('A1', 'LAPORAN PENJUALAN APOTEK NAUFAL');
        $sheet->setCellValue('A2', 'Periode: ' . $from->format('d M Y') . ' - ' . $to->format('d M Y'));
        $sheet->setCellValue('A3', 'Tanggal Export: ' . now()->format('d M Y H:i:s'));

        // Merge title rows across columns A to H so they don't blow up Column A's width
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->mergeCells('A3:H3');

        // Summary section placed in columns B to E to keep Column A clean and compact
        $sheet->setCellValue('B5', 'Total Pendapatan');
        $sheet->setCellValue('B6', (float) $totalPendapatan);
        $sheet->setCellValue('C5', 'Total Order');
        $sheet->setCellValue('C6', $orders->count());
        $sheet->setCellValue('D5', 'Order Selesai');
        $sheet->setCellValue('D6', $orderSelesai);
        $sheet->setCellValue('E5', 'Order Batal');
        $sheet->setCellValue('E6', $orderDibatalkan);

        // Column headers
        $headers = ['No', 'Nomor Order', 'Tanggal Order', 'Nama Pelanggan', 'Metode Bayar', 'Status Pembayaran', 'Status Order', 'Total Harga'];
        $col = 1;
        foreach ($headers as $header) {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . '9';
            $sheet->setCellValue($cell, $header);
            $col++;
        }

        // Data rows
        $row = 10;
        $no = 1;
        foreach ($orders as $order) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $order->order_number);
            $sheet->setCellValue('C' . $row, $order->created_at->format('d M Y H:i'));
            $sheet->setCellValue('D' . $row, $order->user->name ?? 'N/A');
            $sheet->setCellValue('E' . $row, $order->payment_method_label);
            $sheet->setCellValue('F' . $row, $order->payment_status_label);
            $sheet->setCellValue('G' . $row, $order->status_label);
            $sheet->setCellValue('H' . $row, (float) $order->total_amount);
            $row++;
        }

        // Title styling
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:A3')->getFont()->setSize(10)->getColor()->setRGB('718096'); // Subtle gray text

        // Format B6 and H column as Indonesian Rupiah Currency, and center-align Column A numbers
        $sheet->getStyle('B6')->getNumberFormat()->setFormatCode('"Rp "#,##0');
        if ($row > 10) {
            $sheet->getStyle('H10:H' . ($row - 1))->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $sheet->getStyle('A10:A' . ($row - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        // Summary Card Styling (Premium slate-light layout spanning columns B to E)
        $summaryCardStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'CBD5E1'], // Slate-300 border
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F8FAFC'], // Slate-50 background
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('B5:E6')->applyFromArray($summaryCardStyle);
        $sheet->getStyle('B5:E5')->getFont()->setBold(true)->setSize(10)->getColor()->setRGB('475569'); // Slate-600
        $sheet->getStyle('B6:E6')->getFont()->setBold(true)->setSize(11);

        // Header style (row 9)
        $headerStyle = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '346739'], // Apotek Naufal Primary Forest Green
            ],
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('A9:H9')->applyFromArray($headerStyle);

        // Grid lines / borders for table data
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'E2E8F0'], // Light gray borders
                ],
            ],
        ];
        if ($row > 10) {
            $sheet->getStyle('A9:H' . ($row - 1))->applyFromArray($borderStyle);
        }

        // Auto size columns B to H, keeping A at a fixed narrow width for 'No'
        foreach (range('B', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(8);

        // Create writer and output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        $fileName = 'Laporan_Penjualan_' . $from->format('d-m-Y') . '_to_' . $to->format('d-m-Y') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        $writer->save('php://output');
        exit;
    }
}

