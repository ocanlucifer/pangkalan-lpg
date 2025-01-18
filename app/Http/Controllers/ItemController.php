<?php

// app/Http/Controllers/ItemController.php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Type;
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
                return $query->where('items.name', 'LIKE', "%$search%")  // Specify items.name
                            ->orWhere('items.price', 'LIKE', "%$search%")
                            ->orWhere('items.stock', 'LIKE', "%$search%")
                            ->orWhereHas('category', function ($query2) use ($search) {
                                $query2->where('categories.name', 'LIKE', "%$search%");  // Specify categories.name
                            })
                            ->orWhereHas('type', function ($query3) use ($search) {
                                $query3->where('types.name', 'LIKE', "%$search%");  // Specify types.name
                            });
            })
            ->with(['category', 'type']);

        // Handle sorting logic
        if ($sortBy === 'category_name') {
            // If sorting by category_name, join with categories table and order by category name
            $query->join('categories', 'items.category_id', '=', 'categories.id')
                ->select('items.*', 'categories.name as category_name')  // Select items columns and alias category.name
                ->orderBy('categories.name', $order);  // Sort by the category's name column
        } else if($sortBy === 'type_name') {
            // If sorting by type_name, join with types table and order by category name
            $query->join('types', 'items.type_id', '=', 'types.id')
                ->select('items.*', 'types.name as type_name')  // Select items columns and alias type.name
                ->orderBy('types.name', $order);  // Sort by the type's name column
        } else {
            // Otherwise, use the normal sortBy (e.g., 'name', 'price', etc.)
            $query->select('items.*')  // Select only items columns if sorting by item fields
                ->orderBy($sortBy, $order);
        }

        // Get paginated results
        $items = $query->paginate($perPage);
        $categories = Category::All();
        $types = Type::All();

        // If the request is an AJAX request, return only the table view
        if ($request->ajax()) {
            return view('items.table', compact('items', 'search', 'sortBy', 'order', 'perPage', 'categories', 'types'));
        }

        // Return the full page view
        return view('items.index', compact('items', 'search', 'sortBy', 'order', 'perPage', 'categories', 'types'));
    }

    public function create()
    {
        $categories = Category::All();
        $types = Type::All();
        return view('items.create', compact('categories', 'types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'type_id' => 'required|exists:types,id',
            'price' => 'required|numeric',
            'active' => 'required|boolean', // Validasi kolom active
        ]);
        Item::create([
            'name'  => $request->name,
            'category_id'   => $request->category_id,
            'type_id'       => $request->type_id,
            'price'         => $request->price,
            'stock'         => 0,
            'active'        => $request->active ?? true,
            'user_id' => Auth::User()->id,
        ]);

        // return redirect()->route('items.index')->with('success', 'Data Barang Berhasil di Simpan.');
        return response()->json(['success' => 'Data Barang Berhasil di Simpan.']);
    }

    public function edit(Item $item)
    {
        $categories = Category::All();
        $types = Type::All();
        return view('items.edit', compact('item', 'categories', 'types'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'type_id' => 'required|exists:types,id',
            'price' => 'required|numeric',
            'active' => 'required|boolean', // Validasi kolom active
        ]);
        $item->update([
            'name'  => $request->name,
            'category_id'   => $request->category_id,
            'type_id'       => $request->type_id,
            'price'         => $request->price,
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
