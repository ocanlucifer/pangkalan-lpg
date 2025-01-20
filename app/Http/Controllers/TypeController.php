<?php

// app/Http/Controllers/TypeController.php

namespace App\Http\Controllers;

use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TypeController extends Controller
{
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
        $types = Type::when($search, function($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy($sortBy, $order)
            ->paginate($perPage);

        // Cek apakah request adalah AJAX
        if ($request->ajax()) {
            return view('types.table', compact('types', 'search', 'sortBy', 'order', 'perPage'))->render();
        }

        return view('types.index', compact('types', 'search', 'sortBy', 'order', 'perPage'));
    }

    public function create()
    {
        return view('types.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Type::create([
            'name' => $request->name,
            'discount' => $request->discount,
            'limit_trx' => $request->limit_trx,
            'user_id' => Auth::User()->id,
        ]);
        // return redirect()->route('types.index')->with('success', 'Type created successfully.');
        return response()->json(['message' => 'Data Tipe Berhasil Di Buat!'], 201);
    }

    public function edit(Type $type)
    {
        return view('types.edit', compact('type'));
    }

    public function update(Request $request, Type $type)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $type->update([
            'name' => $request->name,
            'discount' => $request->discount,
            'limit_trx' => $request->limit_trx,
            'user_id' => Auth::User()->id,
        ]);
        // return redirect()->route('types.index')->with('success', 'Type updated successfully.');
        return response()->json(['message' => 'Data Tipe Berhasil Di Ubah!'], 200);
    }

    public function destroy(Type $type)
    {
        $type->delete();
        // return redirect()->route('types.index')->with('success', 'Type deleted successfully.');
        return response()->json(['message' => 'Data Tipe Berhasil di hapus!'], 200);
    }
}

