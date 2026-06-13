<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Medicine;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        
        $query = Medicine::with('category')->where('is_active', true);
        
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('brand', 'like', '%' . $searchTerm . '%')
                  ->orWhere('indications', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }
        
        $medicines = $query->get();
        
        return view('landing_page', compact('categories', 'medicines'));
    }

    public function category(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $categories = Category::all();
        
        $query = Medicine::with('category')->where('category_id', $category->id)->where('is_active', true);
        
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $medicines = $query->get();
        
        return view('category', compact('category', 'categories', 'medicines'));
    }

    public function show($slug)
    {
        $medicine = Medicine::with(['category', 'ratings.user'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
        $categories = Category::all();
        $ratings = $medicine->ratings()->with('user')->latest()->get();
        
        return view('product_detail', compact('medicine', 'categories', 'ratings'));
    }

    public function storeReview(Request $request, $slug)
    {
        $medicine = Medicine::where('slug', $slug)->where('is_active', true)->firstOrFail();
        
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);
        
        $existing = \App\Models\Rating::where('medicine_id', $medicine->id)
            ->where('user_id', auth()->id())
            ->first();
            
        if ($existing) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk obat ini.');
        }
        
        \App\Models\Rating::create([
            'medicine_id' => $medicine->id,
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'review' => $request->review,
        ]);
        
        return back()->with('success', 'Ulasan Anda berhasil ditambahkan!');
    }

    public function suggestions(Request $request)
    {
        $search = $request->query('q');
        if (empty($search)) {
            return response()->json([]);
        }

        $medicines = Medicine::with('category')
            ->where('is_active', true)
            ->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('brand', 'like', '%' . $search . '%')
                  ->orWhere('indications', 'like', '%' . $search . '%');
            })
            ->take(5)
            ->get();

        $results = $medicines->map(function ($medicine) {
            return [
                'name' => $medicine->name,
                'slug' => $medicine->slug,
                'price' => number_format($medicine->price, 0, ',', '.'),
                'image' => $medicine->image ? (str_starts_with($medicine->image, '/') ? $medicine->image : '/' . $medicine->image) : null,
                'category_name' => $medicine->category->name ?? 'Umum',
            ];
        });

        return response()->json($results);
    }
}
