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

    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $categories = Category::all();
        $medicines = Medicine::with('category')->where('category_id', $category->id)->where('is_active', true)->get();
        
        return view('category', compact('category', 'categories', 'medicines'));
    }

    public function show($slug)
    {
        $medicine = Medicine::with('category')->where('slug', $slug)->where('is_active', true)->firstOrFail();
        $categories = Category::all();
        
        return view('product_detail', compact('medicine', 'categories'));
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
