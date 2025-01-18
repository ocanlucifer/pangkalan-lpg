<?php

// app/Http/Controllers/VendorController.php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'name');
        $order = $request->input('order', 'asc');
        $perPage = $request->input('per_page', 10);


        $vendors = Vendor::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%$search%")
                             ->orWhere('contact', 'like', "%$search%")
                             ->orWhere('address', 'like', "%$search%");
            })
            ->orderBy($sortBy, $order)
            ->paginate($perPage);

        if ($request->ajax()) {
            return view('vendors.table', compact('vendors', 'search', 'sortBy', 'order', 'perPage'))->render();
        }

        return view('vendors.index', compact('vendors', 'search', 'sortBy', 'order', 'perPage'));
    }

    public function create()
    {
        return view('vendors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'active' => 'required|boolean',
        ]);
        $request->merge(['user_id' => Auth::User()->id]);
        Vendor::create($request->all());
        // return redirect()->route('vendors.index')->with('success', 'Vendor created successfully.');
        return response()->json(['success' => 'Data Supplier Berhasil di Simpan.']);
    }

    public function edit(Vendor $vendor)
    {
        return view('vendors.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'active' => 'required|boolean',
        ]);
        $request->merge(['user_id' => Auth::User()->id]);
        $vendor->update($request->all());
        // return redirect()->route('vendors.index')->with('success', 'Vendor updated successfully.');
        return response()->json(['success' => 'Data Supplier Berhasil di Ubah.']);
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();
        // return redirect()->route('vendors.index')->with('success', 'Vendor deleted successfully.');
        return response()->json(['success' => 'Data Supplier Berhasil Di Hapus.']);
    }

    // Toggle the active status of a vendor
    public function toggleActive(Vendor $vendor)
    {
        $vendor->active = !$vendor->active; // Toggle the active flag
        $vendor->save();

        // return redirect()->route('vendors.index')->with('success', 'Vendor status updated.');
        return response()->json(['success' => 'Status Supplier Berhasil Di Perbarui.']);
    }
}
