<?php

// app/Http/Controllers/CategoryController.php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    // public function index()
    // {
    //     $categories = Category::all();
    //     return view('categories.index', compact('categories'));
    // }
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $sortBy = $request->input('sort_by', 'name');
        $order = $request->input('order', 'asc');
        $perPage = $request->input('per_page', 10);

        // Validasi input, pastikan sort_by dan order sesuai yang diizinkan
        $allowedSortBy = ['name', 'created_at'];
        $allowedOrder = ['asc', 'desc'];

        if (!in_array($sortBy, $allowedSortBy)) {
            $sortBy = 'name'; // Default sort
        }

        if (!in_array($order, $allowedOrder)) {
            $order = 'asc'; // Default order
        }

        // Ambil kategori berdasarkan pencarian dan pengurutan
        $categories = Category::when($search, function($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy($sortBy, $order)
            ->paginate($perPage);

        // Cek apakah request adalah AJAX
        if ($request->ajax()) {
            return view('categories.table', compact('categories', 'search', 'sortBy', 'order', 'perPage'))->render();
        }

        return view('categories.index', compact('categories', 'search', 'sortBy', 'order', 'perPage'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Category::create([
            'name' => $request->name,
            'user_id' => Auth::User()->id,
        ]);
        // return redirect()->route('categories.index')->with('success', 'Kategori Berhasil Di Simpan.');

        return response()->json(['message' => 'Kategori Berhasil Di Simpan!'], 201);
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $category->update([
            'name' => $request->name,
            'user_id' => Auth::User()->id,
        ]);
        return response()->json(['message' => 'Kategori Berhasil Di Ubah!'], 200);
        // return redirect()->route('categories.index')->with('success', 'Kategori Berhasil Di Ubah.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(['message' => 'Kategori Berhasil Di Hapus!'], 200);
        // return redirect()->route('categories.index')->with('success', 'Kategori Berhasil Di Hapus.');
    }

}
