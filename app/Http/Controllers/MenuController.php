<?php

// app/Http/Controllers/MenuController.php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Category;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    // public function index()
    // {
    //     $menus = Item::IsMenu()->with(['category', 'type'])->get();
    //     return view('menus.index', compact('menus'));
    // }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'name');
        $order = $request->input('order', 'asc');
        $perPage = $request->input('per_page', 10);

        // Start building the query for items
        $query = MenuItem::query()
            ->when($search, function ($query, $search) {
                return $query->where('menu_items.name', 'LIKE', "%$search%")  // Specify menu_items.name
                            ->orWhere('menu_items.price', 'LIKE', "%$search%")
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
            $query->join('categories', 'menu_items.category_id', '=', 'categories.id')
                ->select('menu_items.*', 'categories.name as category_name')  // Select items columns and alias category.name
                ->orderBy('categories.name', $order);  // Sort by the category's name column
        } else if($sortBy === 'type_name') {
            // If sorting by type_name, join with types table and order by category name
            $query->join('types', 'menu_items.type_id', '=', 'types.id')
                ->select('menu_items.*', 'types.name as type_name')  // Select items columns and alias type.name
                ->orderBy('types.name', $order);  // Sort by the type's name column
        } else {
            // Otherwise, use the normal sortBy (e.g., 'name', 'price', etc.)
            $query->select('menu_items.*')  // Select only items columns if sorting by item fields
                ->orderBy($sortBy, $order);
        }

        // Get paginated results
        $menus = $query->paginate($perPage);
        $categories = Category::All();
        $types = Type::All();

        // If the request is an AJAX request, return only the table view
        if ($request->ajax()) {
            return view('menus.table', compact('menus', 'search', 'sortBy', 'order', 'perPage', 'categories', 'types'));
        }

        // Return the full page view
        return view('menus.index', compact('menus', 'search', 'sortBy', 'order', 'perPage', 'categories', 'types'));
    }

    public function create()
    {
        $categories = Category::all();
        $types = Type::All();
        return view('menus.create', compact('categories', 'types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric',
            'active' => 'required|boolean', // Validasi kolom active
        ]);
        MenuItem::create([
            'name'  => $request->name,
            'category_id'   => $request->category_id,
            'type_id'       => $request->type_id,
            'price'         => $request->price,
            'active'        => $request->active ?? true,
            'user_id' => Auth::User()->id,
        ]);
        // return redirect()->route('menus.index')->with('success', 'Item created successfully.');
        return response()->json(['success' => 'Data Menu Berhasil Di simpan.']);
    }

    public function edit(MenuItem $item)
    {
        $categories = Category::all();
        $types = Type::All();
        return view('menus.edit', compact('item', 'categories', 'types'));
    }

    public function update(Request $request, MenuItem $menu)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric',
            'active' => 'required|boolean', // Validasi kolom active
        ]);
        $menu->update([
            'name'  => $request->name,
            'category_id'   => $request->category_id,
            'type_id'       => $request->type_id,
            'price'         => $request->price,
            'active'        => $request->active ?? true,
            'user_id' => Auth::User()->id,
        ]);
        // return redirect()->route('menus.index')->with('success', 'Item updated successfully.');
        return response()->json(['success' => 'Data Menu berhasil di ubah.']);
    }

    public function destroy(MenuItem $menu)
    {
        $menu->delete();
        // return redirect()->route('menus.index')->with('success', 'Item deleted successfully.');
        return response()->json(['success' => 'Data Menu berhasil di hapus.']);
    }

    public function toggleActive(MenuItem $menu)
    {
        // Toggle the active status
        $menu->active = !$menu->active;
        $menu->save();

        // return redirect()->route('menus.index');
        return response()->json(['success' => 'Status Menu berhasil di perbarui.']);
    }

}
