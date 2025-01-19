<?php

// app/Http/Controllers/ItemController.php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'name');
        $order = $request->input('order', 'asc');
        $perPage = $request->input('per_page', 10);

        // Start building the query for items
        $query = Item::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'LIKE', "%$search%") ;
            })
            ->orderBy($sortBy, $order);

        // Get paginated results
        $items = $query->paginate($perPage);

        // If the request is an AJAX request, return only the table view
        if ($request->ajax()) {
            return view('items.table', compact('items', 'search', 'sortBy', 'order', 'perPage'));
        }
        // if ($request->ajax()) {
        //     if ($request->param == 1){
        //         return view('items.index', compact('items', 'search', 'sortBy', 'order', 'perPage'))->render();
        //     } else {
        //         return view('items.table', compact('items', 'search', 'sortBy', 'order', 'perPage'))->render();
        //     }
        // }

        // Return the full page view
        return view('items.index', compact('items', 'search', 'sortBy', 'order', 'perPage'));
    }

    public function create()
    {
        return view('items.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'buy_price' => 'required|numeric',
            'sell_price' => 'required|numeric',
            'active' => 'required|boolean', // Validasi kolom active
        ]);
        Item::create([
            'name'  => $request->name,
            'buy_price'         => $request->buy_price,
            'sell_price'         => $request->sell_price,
            'stock'         => 0,
            'active'        => $request->active ?? true,
            'user_id' => Auth::User()->id,
        ]);

        // return redirect()->route('items.index')->with('success', 'Data Barang Berhasil di Simpan.');
        return response()->json(['success' => 'Data Barang Berhasil di Simpan.']);
    }

    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'buy_price' => 'required|numeric',
            'sell_price' => 'required|numeric',
            'active' => 'required|boolean', // Validasi kolom active
        ]);
        $item->update([
            'name'  => $request->name,
            'buy_price'         => $request->buy_price,
            'sell_price'         => $request->sell_price,
            'active'        => $request->active ?? true,
            'user_id' => Auth::User()->id,
        ]);
        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'category_id' => 'required|exists:categories,id',
        //     'type_id' => 'required|exists:types,id',
        //     'price' => 'required|numeric',
        //     'stock' => 'required|integer',
        //     'active' => 'required|boolean', // Validasi kolom active
        // ]);
        // $item->update($request->all());
        // return redirect()->route('items.index')->with('success', 'Data Barang Berhasil Di Ubah.');
        return response()->json(['success' => 'Data Barang Berhasil Di Ubah.']);
    }

    public function destroy(Item $item)
    {
        $item->delete();
        // return redirect()->route('items.index')->with('success', 'Data Barang Berhasil Di Hapus.');
        return response()->json(['success' => 'Data Barang Berhasil Di Hapus.']);
    }

    public function toggleActive(Item $item)
    {
        // Toggle the active status
        $item->active = !$item->active;
        $item->save();

        // return redirect()->route('items.index');
        return response()->json(['success' => 'Status Barang Sudah Di Perbarui.']);
    }

}
