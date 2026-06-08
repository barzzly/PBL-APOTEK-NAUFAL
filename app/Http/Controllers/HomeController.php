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

    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $categories = Category::all();
        $medicines = Medicine::with('category')->where('category_id', $category->id)->where('is_active', true)->get();
        
        return view('category', compact('category', 'categories', 'medicines'));
    }
}
