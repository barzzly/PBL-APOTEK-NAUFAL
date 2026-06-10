<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Medicine;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

        // Low stock medicines (stock <= 10)
        $lowStockMedicines = Medicine::where('stock', '<=', 10)
                                     ->orderBy('stock')
                                     ->limit(5)
                                     ->get();

        // Recent orders
        $recentOrders = Order::with('user')
                             ->latest()
                             ->limit(6)
                             ->get();

        return view('admin.dashboard', compact(
            'categoriesCount', 'medicinesCount',
            'totalOrders', 'ordersPending', 'ordersToday',
            'revenueToday', 'revenueMonth',
            'lowStockMedicines', 'recentOrders'
        ));
    }

    // --- Categories ---
    public function categories()
    {
        $categories = Category::all();
        return view('admin.categories', compact('categories'));
    }

    public function createCategory()
    {
        return view('admin.categories_create');
    }

    public function storeCategory(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        
        $imageName = null;
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
        }

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $imageName ? '/images/' . $imageName : null,
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
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $category->image = '/images/' . $imageName;
        }

        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->save();

        return redirect()->route('admin.categories')->with('success', 'Kategori berhasil diperbarui!');
    }

    public function deleteCategory($id)
    {
        Category::findOrFail($id)->delete();
        return back()->with('success', 'Kategori berhasil dihapus!');
    }

    // --- Medicines ---
    public function medicines()
    {
        $medicines = Medicine::with('category')->get();
        return view('admin.medicines', compact('medicines'));
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
        }

        Medicine::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $imageName ? '/images/' . $imageName : null,
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $medicine->image = '/images/' . $imageName;
        }

        $medicine->category_id = $request->category_id;
        $medicine->name = $request->name;
        $medicine->slug = Str::slug($request->name);
        $medicine->price = $request->price;
        $medicine->stock = $request->stock;
        $medicine->save();

        return redirect()->route('admin.medicines')->with('success', 'Obat berhasil diperbarui!');
    }

    public function deleteMedicine($id)
    {
        Medicine::findOrFail($id)->delete();
        return back()->with('success', 'Obat berhasil dihapus!');
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

        // Base query builder
        $baseQuery = Order::with('user')
            ->whereBetween('created_at', [$from, $to]);

        if ($statusFilter !== 'all') {
            $baseQuery->where('status', $statusFilter);
        }
        if ($paymentFilter !== 'all') {
            $baseQuery->where('payment_method', $paymentFilter);
        }

        // Summary cards
        $totalPendapatan = (clone $baseQuery)->where('payment_status', 'paid')->sum('total_amount');
        $totalOrder      = (clone $baseQuery)->count();
        $orderSelesai    = (clone $baseQuery)->where('status', 'delivered')->count();
        $orderDibatalkan = (clone $baseQuery)->where('status', 'cancelled')->count();

        // Orders list with pagination
        $orders = (clone $baseQuery)->latest()->paginate(15)->withQueryString();

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
            'from', 'to'
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
}
