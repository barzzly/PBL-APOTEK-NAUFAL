<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        $categoriesCount = Category::count();
        $medicinesCount = Medicine::count();
        return view('admin.dashboard', compact('categoriesCount', 'medicinesCount'));
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
}
