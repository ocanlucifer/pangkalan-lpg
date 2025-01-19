<?php

// app/Http/Controllers/CustomerController.php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'name');
        $order = $request->input('order', 'asc');
        $perPage = $request->input('per_page', 10);

        $customers = Customer::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%$search%")
                            ->orWhere('contact', 'like', "%$search%")
                            ->orWhere('address', 'like', "%$search%");
            })
            ->orderBy($sortBy, $order)
            ->paginate($perPage);

        // if ($request->ajax()) {
        //     return view('customers.table', compact('customers', 'search', 'sortBy', 'order', 'perPage'))->render();
        // }

        if ($request->ajax()) {
            if ($request->param == 1){
                return view('customers.index', compact('customers', 'search', 'sortBy', 'order', 'perPage'))->render();
            } else {
                return view('customers.table', compact('customers', 'search', 'sortBy', 'order', 'perPage'))->render();
            }
        }

        return view('customers.index', compact('customers', 'search', 'sortBy', 'order', 'perPage'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nik' => 'required|string|max:255|unique:customers,nik',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'active' => 'required|boolean',
            'ktp_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validasi file gambar
        ]);

        // Handle file upload
        if ($request->hasFile('ktp_image')) {
            $validatedData['ktp_image'] = $request->file('ktp_image')->store('uploads/ktp_images', 'public');
        }

        $validatedData['user_id'] = Auth::User()->id; // ID pengguna saat ini
        Customer::create($validatedData);

        return response()->json(['message' => 'Pelanggan berhasil ditambahkan']);
    }

    public function update(Request $request, Customer $customer)
    {
        $validatedData = $request->validate([
            'nik' => 'required|string|max:255|unique:customers,nik,' . $customer->id,
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'active' => 'required|boolean',
            'ktp_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle file upload
        if ($request->hasFile('ktp_image')) {
            // Hapus file lama jika ada
            if ($customer->ktp_image) {
                Storage::disk('public')->delete($customer->ktp_image);
            }
            $validatedData['ktp_image'] = $request->file('ktp_image')->store('uploads/ktp_images', 'public');
        }

        $customer->update($validatedData);

        return response()->json(['message' => 'Pelanggan berhasil diperbarui']);
    }

    public function destroy(Customer $customer)
    {
        // Hapus file lama jika ada
        if ($customer->ktp_image) {
            Storage::disk('public')->delete($customer->ktp_image);
        }
        $customer->delete();
        // return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
        return response()->json(['message' => 'DAta Pelanggan Berhasil Di Hapus']);
    }

    public function toggleActive(Customer $customer)
    {
        // Toggle the active status
        $customer->active = !$customer->active;
        $customer->save();

        // return redirect()->route('customers.index');
        return response()->json(['message' => 'Status Pelanggan Berhasil Di Ubah']);
    }

}
