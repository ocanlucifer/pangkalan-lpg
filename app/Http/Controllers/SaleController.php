<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SalesDetail;
use App\Models\MenuItem;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\SalesExport;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    /**
     * Tampilkan daftar transaksi.
     */
    public function index(Request $request)
    {
        $sortBy = $request->get('sort_by', 'transaction_number');  // Default sorting berdasarkan transaction_number
        $order = $request->get('order', 'asc');  // Default ascending
        // Get search query from the request
        $search = $request->get('search', '');
        $perPage = $request->get('per_page', 10); // Default to 10 per page
        $fromDate = $request->input('from_date', now()->toDateString());
        $toDate = $request->input('to_date', now()->toDateString());

        $sales = Sale::query()
                    ->when($search, function ($query, $search) {
                        return $query->where('transaction_number', 'like', "%$search%")
                                    ->orWhereHas('customer', function ($query) use ($search) {
                                        $query->where('name', 'like', "%$search%");
                                    });
                                })
                    ->with(['customer', 'details.menuItem']);
        // Handle sorting logic
        if ($sortBy === 'customer_name') {
            // If sorting by category_name, join with categories table and order by category name
            $sales->join('customers', 'sales.customer_id', '=', 'customers.id')
                ->select('sales.*', 'customers.name as customer_name')  // Select items columns and alias category.name
                ->orderBy('customers.name', $order);  // Sort by the category's name column
        } else {
            // Otherwise, use the normal sortBy (e.g., 'name', 'price', etc.)
            $sales->select('sales.*')  // Select only items columns if sorting by item fields
                ->orderBy($sortBy, $order);
        }

        if ($fromDate && $toDate) {
            // Menggunakan whereBetween untuk filter tanggal dari 'fromDate' ke 'toDate' dengan waktu penuh
            $sales->whereBetween('sales.created_at', [
                $fromDate . ' 00:00:00', // Mulai dari awal hari
                $toDate . ' 23:59:59'    // Sampai akhir hari
            ]);
        } elseif ($fromDate) {
            // Jika hanya ada 'fromDate', maka cari transaksi yang terjadi pada atau setelah 'fromDate'
            $sales->whereDate('sales.created_at', '>=', $fromDate);
        } elseif ($toDate) {
            // Jika hanya ada 'toDate', maka cari transaksi yang terjadi pada atau sebelum 'toDate'
            $sales->whereDate('sales.created_at', '<=', $toDate);
        }

        $result = $sales->paginate($perPage);
        // $sales = Sale::with('customer', 'details.item')->get();

        // Menambahkan perhitungan Total Sebelum Diskon, Total Diskon, dan Total Setelah Diskon
        foreach ($result as $sale) {
            // Hitung total sebelum diskon
            $totalBeforeDiscount = $sale->details->sum(function($detail) {
                return $detail->price * $detail->quantity;
            });

            // Hitung total diskon per item
            $totalItemDiscount = $sale->details->sum(function($detail) {
                return $detail->discount;
            });

            // Total harga setelah diskon per item
            $totalAfterItemDiscount = $totalBeforeDiscount - $totalItemDiscount;

            // Total harga setelah semua diskon
            $totalPriceAfterDiscount = $totalAfterItemDiscount - $sale->discount;

            // Menyimpan perhitungan ke atribut sale
            $sale->total_before_discount = $totalBeforeDiscount;
            $sale->total_item_discount = $totalItemDiscount;
            $sale->total_after_discount = $totalPriceAfterDiscount;
        }

        // If the request is an AJAX request, return the partial view with the table
        if ($request->ajax()) {
            return view('sales.table', compact('result', 'search', 'perPage', 'sortBy', 'order', 'fromDate', 'toDate'));
        }

        // Otherwise, return the full index view
        return view('sales.index', compact('result', 'search', 'perPage', 'sortBy', 'order', 'fromDate', 'toDate'));
    }


    /**
     * Form untuk membuat transaksi baru.
     */
    public function create()
    {
        $customers = Customer::where('Active',1)->get(); // Mengambil semua data customer
        $items = MenuItem::where('Active',1)->get(); // Mengambil semua data item

        return view('sales.create', compact('customers', 'items'));
    }

    /**
     * Simpan transaksi baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $validatedData = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.discount' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0', // Diskon header
            'payment_amount' => 'required|numeric|min:0',//|gte:grand_total', // Payment must be greater than or equal to grand total
            'change_amount' => 'required|numeric|min:0',//|lte:payment_amount', // Change must be less than or equal to payment amount
        ]);
        // Mulai transaksi database
        $id = DB::transaction(function () use ($validatedData) {
                // Hitung total harga dan total diskon
                $totalPrice = 0;
                $totalDiscount = 0;

                // Loop untuk menghitung harga dan diskon per item
                foreach ($validatedData['items'] as $item) {
                    $itemDetails = MenuItem::findOrFail($item['item_id']);
                    $itemPrice = $itemDetails->price;
                    $quantity = $item['quantity'];
                    $discount = $item['discount'] ?? 0; // Diskon per item

                    // Hitung subtotal per item setelah diskon
                    $subtotal = ($itemPrice * $quantity) - $discount;

                    // Menambahkan subtotal dan diskon per item ke total
                    $totalPrice += $subtotal;
                    $totalDiscount += $discount;
                }

                // Simpan header transaksi (sale)
                $sale = Sale::create([
                    'customer_id' => $validatedData['customer_id'],
                    'total_price' => $totalPrice - ($validatedData['discount'] ?? 0), // Total setelah diskon header
                    'discount' => $validatedData['discount'] ?? 0, // Diskon header
                    'peyment_amount'    => $validatedData['payment_amount'],
                    'change_amount'     => $validatedData['change_amount'],
                    'user_id' => Auth::User()->id,
                    // 'transaction_number' => Sale::generateTransactionNumber(), // Nomor transaksi unik
                ]);

                // Simpan detail transaksi (sales_detail)
                foreach ($validatedData['items'] as $item) {
                    $itemDetails = MenuItem::findOrFail($item['item_id']);
                    $itemPrice = $itemDetails->price;
                    $quantity = $item['quantity'];
                    $discount = $item['discount'] ?? 0;

                    // Simpan detail transaksi
                    SalesDetail::create([
                        'sales_id' => $sale->id,
                        'menu_item_id' => $item['item_id'],
                        'quantity' => $quantity,
                        'price' => $itemPrice,
                        'subtotal' => ($itemPrice * $quantity) - $discount, // Subtotal per item setelah diskon
                        'discount' => $discount, // Diskon per item
                    ]);
                }
                return $sale->id;
            });

        session()->flash('success', 'Transaksi Penjualan berhasil dibuat!');

        return redirect()->route('sales.show',$id);
    }

    // Fungsi untuk menampilkan halaman Edit Transaksi
    public function edit($id)
    {
        // Mengambil data transaksi yang akan diedit
        $sale = Sale::with('details.menuItem', 'customer')->findOrFail($id);

        // Mengambil semua data customer dan item untuk ditampilkan dalam dropdown
        $customers = Customer::where('Active',1)->get(); // Mengambil semua data customer
        $items = MenuItem::where('Active',1)->get(); // Mengambil semua data item

        // Menampilkan halaman edit dengan data transaksi yang akan diedit
        return view('sales.edit', compact('sale', 'customers', 'items'));
    }

    // Fungsi untuk memperbarui transaksi
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'discount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.discount' => 'nullable|numeric|min:0',
            'payment_amount' => 'required|numeric|min:0', //|gte:grand_total', // Payment must be greater than or equal to grand total
            'change_amount' => 'required|numeric|min:0', //|lte:payment_amount', // Change must be less than or equal to payment amount
        ]);

        // Ambil transaksi yang akan diperbarui
        $sale = Sale::findOrFail($id);

        // Update data transaksi
        $sale->customer_id = $request->customer_id;
        $sale->discount = $request->discount ?? 0;
        $sale->payment_amount = $request->payment_amount;
        $sale->change_amount = $request->change_amount;
        $sale->user_id = Auth::User()->id;
        $sale->save();

        // Hapus detail lama jika ada
        $sale->details()->delete();

        foreach ($request->items as $itemData) {
            $item = MenuItem::find($itemData['item_id']);
            $discount = $itemData['discount'] ?? 0;
            $subtotal = ($item->price * $itemData['quantity']) - $discount;

            // Menambahkan detail transaksi
            $sale->details()->create([
                'menu_item_id' => $itemData['item_id'],
                'quantity' => $itemData['quantity'],
                'discount' => $discount,
                'price' => $item->price,
                'subtotal' => $subtotal,
            ]);
        }

        // Redirect kembali ke halaman daftar transaksi dengan pesan sukses
        session()->flash('success', 'Transaksi Penjualan berhasil di ubah');

        return redirect()->route('sales.show', $id);
    }

    /**
     * Tampilkan detail transaksi tertentu.
     */
    public function show(Sale $sale)
    {
        $sale->load('customer', 'details.menuItem'); // Men-load relasi customer dan detail transaksi

        // Menghitung total diskon dan total harga setelah diskon
        $totalDiscount = $sale->calculateTotalDiscount(); // Menghitung total diskon
        $totalPriceAfterDiscount = $sale->calculateTotalPrice(); // Menghitung total harga setelah diskon

        return view('sales.show', compact('sale', 'totalDiscount', 'totalPriceAfterDiscount'));
    }

    /**
     * Hapus transaksi.
     */
    public function destroy(Sale $sale)
    {
        $sale->delete();

        session()->flash('success', 'Transaksi Penjualan berhasil dihapus!');

        return redirect()->route('sales.index');
    }

    //report
    // Controller method untuk generate sales report
    public function generateSalesReport(Request $request)
    {
        // Filter berdasarkan tanggal (opsional)
        $fromDate = Carbon::parse($request->input('from_date', Carbon::now()->startOfDay()));
        $toDate = Carbon::parse($request->input('to_date', Carbon::now()->endOfDay()));

        // Tentukan waktu yang lebih presisi
        $fromDate = $fromDate->startOfDay();  // 00:00:00
        $toDate = $toDate->endOfDay();  // 23:59:59

        $perPage = $request->get('per_page', 10); // Default to 10 per page

        // Ambil filter pilihan grup
        $group = $request->input('group', 'customer'); // Default 'customer'

        // Default: laporan per customer
        if ($group == 'customer') {
            $sales = DB::table('customers')
                        ->select(
                            'customers.id',
                            'customers.name',
                            DB::raw('(
                                SUM(sales.total_price) +
                                (SUM(sales.discount) + COALESCE(SUM(X.discount), 0))
                            ) AS total_before_discount'),
                            DB::raw('(SUM(sales.discount) + COALESCE(SUM(X.discount), 0)) AS total_discount'),
                            DB::raw('SUM(sales.total_price) AS total_after_discount')
                        )
                        ->join('sales', 'customers.id', '=', 'sales.customer_id')
                        ->leftJoinSub(
                            DB::table('customers')
                                ->select(
                                    'customers.id',
                                    DB::raw('SUM(sales_details.discount) AS discount')
                                )
                                ->join('sales', 'customers.id', '=', 'sales.customer_id')
                                ->join('sales_details', 'sales.id', '=', 'sales_details.sales_id')
                                ->groupBy('customers.id'),
                            'X',
                            'customers.id',
                            '=',
                            'X.id'
                        )
                        ->whereBetween('sales.created_at', [$fromDate, $toDate])
                        ->groupBy('customers.id', 'customers.name')
                        ->orderBy('customers.name', 'asc')
                        ->paginate($perPage);

        }

        if ($group == 'item') {
            // Query untuk grup berdasarkan item
            $items = MenuItem::select('menu_items.name')
                ->join('sales_details', 'menu_items.id', '=', 'sales_details.menu_item_id')
                ->join('sales', 'sales_details.sales_id', '=', 'sales.id')
                ->whereBetween('sales.created_at', [$fromDate, $toDate])
                ->selectRaw('SUM(sales_details.quantity) as total_quantity')
                ->selectRaw('SUM(sales_details.subtotal) as total_sales')
                ->groupBy('menu_items.name')
                ->having('total_quantity', '>', 0) // Hanya ambil item dengan total quantity > 0
                ->paginate($perPage);

            // Jika tombol export ditekan
            if ($request->has('export') && $request->input('export') == 'excel') {
                $sales = Sale::with(['customer', 'details.menuItem'])
                    ->whereBetween('created_at', [$fromDate, $toDate])
                    ->get();

                // Lakukan ekspor ke Excel
                return Excel::download(new SalesExport($sales), 'sales_report.xlsx');
            }

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('sales.reports.partials.item_table', compact('items', 'fromDate', 'toDate', 'group', 'perPage'))->render()
                ]);
            }

            return view('sales.reports.report', compact('items', 'fromDate', 'toDate', 'group', 'perPage'));
        }


        // Jika tombol export ditekan
        if ($request->has('export') && $request->input('export') == 'excel') {
            $sales = Sale::with(['customer', 'details.menuItem'])
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->get();

            // Lakukan ekspor ke Excel
            return Excel::download(new SalesExport($sales), 'sales_report.xlsx');
        }

        if ($request->ajax()) {
            return response()->json([
                'html' => view('sales.reports.partials.customer_table', compact('sales', 'fromDate', 'toDate', 'group', 'perPage'))->render()
            ]);
        }

        return view('sales.reports.report', compact('sales', 'fromDate', 'toDate', 'group', 'perPage'));
    }

    public function printPDF($id)
    {
        // Ambil data transaksi berdasarkan ID
        $sale = Sale::with(['customer', 'details.menuItem'])->findOrFail($id);

        // Hitung total transaksi
        $totalBeforeDiscount = $sale->details->sum(function ($detail) {
            return $detail->price * $detail->quantity;
        });
        $totalItemDiscount = $sale->details->sum('discount');
        $totalAfterItemDiscount = $totalBeforeDiscount - $totalItemDiscount;
        $totalPriceAfterDiscount = $totalAfterItemDiscount - $sale->discount;

        // Buat file PDF menggunakan tampilan
        $pdf = PDF::loadView('sales.pdf', [
            'sale' => $sale,
            'totalBeforeDiscount' => $totalBeforeDiscount,
            'totalItemDiscount' => $totalItemDiscount,
            'totalPriceAfterDiscount' => $totalPriceAfterDiscount,
        ])->setPaper([0, 0, 300, 600]) // Atur ukuran kertas ke 250x600 pixel
          ->setOptions(['defaultFont' => 'Arial']) // Opsional: Atur font default
          ->setOption('isHtml5ParserEnabled', true) // Opsi parser
          ->setOption('isRemoteEnabled', true); // Opsi untuk resource eksternal


        // Unduh file PDF
        return $pdf->stream('Transaction_Receipt_' . $sale->transaction_number . '.pdf');
    }

    public function printReportPDF(Request $request)
    {
        // Filter berdasarkan tanggal (opsional)
        $fromDate = Carbon::parse($request->input('from_date', Carbon::now()->startOfDay()));
        $toDate = Carbon::parse($request->input('to_date', Carbon::now()->endOfDay()));

        // Tentukan waktu yang lebih presisi
        $fromDate = $fromDate->startOfDay();  // 00:00:00
        $toDate = $toDate->endOfDay();  // 23:59:59

        // Ambil filter pilihan grup
        $group = $request->input('group', 'customer'); // Default 'customer'

        // Ambil data sales berdasarkan rentang tanggal
        $salesQuery = DB::table('customers')
                        ->select(
                            'customers.id',
                            'customers.name',
                            DB::raw('(
                                SUM(sales.total_price) +
                                (SUM(sales.discount) + COALESCE(SUM(X.discount), 0))
                            ) AS total_before_discount'),
                            DB::raw('(SUM(sales.discount) + COALESCE(SUM(X.discount), 0)) AS total_discount'),
                            DB::raw('SUM(sales.total_price) AS total_after_discount')
                        )
                        ->join('sales', 'customers.id', '=', 'sales.customer_id')
                        ->leftJoinSub(
                            DB::table('customers')
                                ->select(
                                    'customers.id',
                                    DB::raw('SUM(sales_details.discount) AS discount')
                                )
                                ->join('sales', 'customers.id', '=', 'sales.customer_id')
                                ->join('sales_details', 'sales.id', '=', 'sales_details.sales_id')
                                ->groupBy('customers.id'),
                            'X',
                            'customers.id',
                            '=',
                            'X.id'
                        )
                        ->whereBetween('sales.created_at', [$fromDate, $toDate])
                        ->groupBy('customers.id', 'customers.name')
                        ->orderBy('customers.name', 'asc');

        // Jika memilih per customer
        if ($group == 'customer') {
            $sales = $salesQuery->get(); // Ambil semua sales per customer
            $view = 'sales.reports.report_customers_pdf';
            $data = compact('sales', 'fromDate', 'toDate');

        }

        // Jika memilih per item
        if ($group == 'item') {
            // Grouping berdasarkan item
            $items = MenuItem::with(['salesDetails' => function($query) use ($fromDate, $toDate) {
                $query->whereHas('sale', function($q) use ($fromDate, $toDate) {
                    $q->whereBetween('created_at', [$fromDate, $toDate]);
                });
            }])
            ->get()
            ->map(function($item) {
                // Hitung total quantity dan total sales per item
                $totalQuantity = $item->salesDetails->sum('quantity');
                $totalSales = $item->salesDetails->sum(function($detail) {
                    return $detail->subtotal;
                });

                // Hanya kembalikan item yang memiliki total quantity lebih dari 0
                if ($totalQuantity > 0) {
                    return (object)[
                        'name' => $item->name,
                        'total_quantity' => $totalQuantity,
                        'total_sales' => $totalSales
                    ];
                }

                // Jika quantity tidak lebih dari 0, kembalikan null
                return null;
            })
            ->filter() // Filter item yang bernilai null (yaitu item dengan quantity 0)
            ->values(); // Reindex array setelah filter

            $view = 'sales.reports.report_items_pdf';
            $data = compact('items', 'fromDate', 'toDate');
        }

        // Buat PDF dari tampilan yang sesuai
        $pdf = Pdf::loadView($view, $data);

        // Return PDF untuk diunduh atau ditampilkan
        return $pdf->stream('Sales_Report_' . now()->format('Ymd') . '.pdf');
    }
}
