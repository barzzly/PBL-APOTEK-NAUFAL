<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Medicine;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        // Get medicines, optionally group by category or just latest
        $medicines = Medicine::with('category')->where('is_active', true)->get();
        
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
}
