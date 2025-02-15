<?php

namespace App\Http\Controllers;

use App\Exports\PurchaseExport;
use App\Models\PurchaseHeader;
use App\Models\Vendor;
use App\Models\Item;
use App\Models\StockCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    /**
     * Tampilkan daftar pembelian.
     */
    public function index(Request $request)
    {
        $sortBy = $request->get('sort_by', 'transaction_number');  // Default sorting berdasarkan transaction_number
        $order = $request->get('order', 'asc');  // Default ascending
        $search = $request->get('search', '');
        $perPage = $request->get('per_page', 10); // Default to 10 per page
        $fromDate = $request->input('from_date', now()->subDays(7)->toDateString());
        $toDate = $request->input('to_date', now()->toDateString());

        $purchases = PurchaseHeader::query()
                    ->when($search, function ($query, $search) {
                        return $query->where('transaction_number', 'like', "%$search%")
                                    ->orWhereHas('vendor', function ($query) use ($search) {
                                        $query->where('name', 'like', "%$search%");
                                    });
                    })
                    ->with(['vendor', 'details.item']);

        // Handle sorting logic
        if ($sortBy === 'vendor_name') {
            // If sorting by vendor name, join with vendors table and order by vendor name
            $purchases->join('vendors', 'purchase_headers.vendor_id', '=', 'vendors.id')
                ->select('purchase_headers.*', 'vendors.name as vendor_name')
                ->orderBy('vendors.name', $order);
        } else {
            // Otherwise, use the normal sortBy (e.g., 'name', 'price', etc.)
            $purchases->select('purchase_headers.*')
                ->orderBy($sortBy, $order);
        }

        if ($fromDate && $toDate) {
            $purchases->whereBetween('purchase_headers.created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ]);
        } elseif ($fromDate) {
            $purchases->whereDate('purchase_headers.created_at', '>=', $fromDate);
        } elseif ($toDate) {
            $purchases->whereDate('purchase_headers.created_at', '<=', $toDate);
        }

        $result = $purchases->paginate($perPage);

        // If the request is an AJAX request, return the partial view with the table
        if ($request->ajax()) {
            return view('purchases.table', compact('result', 'search', 'perPage', 'sortBy', 'order', 'fromDate', 'toDate'));
        }

        return view('purchases.index', compact('result', 'search', 'perPage', 'sortBy', 'order', 'fromDate', 'toDate'));
    }

    /**
     * Form untuk membuat pembelian baru.
     */
    public function create()
    {
        $vendors = Vendor::where('Active',1)->get();
        $items = Item::where('Active',1)->get();
        return view('purchases.create', compact('vendors', 'items'));
    }

    /**
     * Simpan pembelian baru.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'purchase_date' => 'required|date',
            'total_amount' => 'required|numeric',
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Create purchase header
            $purchaseHeader = PurchaseHeader::create([
                'vendor_id' => $validatedData['vendor_id'],
                'purchase_date' => $validatedData['purchase_date'],
                'total_amount' => $validatedData['total_amount'],
                'user_id' => Auth::User()->id,
            ]);

            // Add purchase details
            foreach ($validatedData['items'] as $item) {
                $purchaseHeader->details()->create([
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total_price' => $item['quantity'] * $item['price'],
                ]);
                $items = Item::find($item['item_id']);
                $stock_card = StockCard::Create([
                    'item_id'               => $item['item_id'],
                    'transaction_number'    => $purchaseHeader->transaction_number,
                    'qty_begin'             => $items->stock,
                    'qty_in'                => $item['quantity'],
                    'qty_out'               => 0,
                    'qty_end'               => $items->stock + $item['quantity']
                ]);
                $items->Update([
                    'stock' => $stock_card->qty_end,
                ]);
            }

            DB::commit();

            return redirect()->route('purchases.index')->with('success', 'Transaksi Pembelian Berhasil di buat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal Membuat Transaksi Pembelian: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan form untuk mengedit pembelian.
     */
    public function edit(PurchaseHeader $purchase)
    {
        $vendors = Vendor::where('Active',1)->get();
        $items = Item::where('Active',1)->get();
        return view('purchases.edit', compact('purchase', 'vendors', 'items'));
    }

    /**
     * Update pembelian.
     */
    public function update(Request $request, PurchaseHeader $purchase)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'purchase_date' => 'required|date',
            'total_amount' => 'required|numeric',
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Update purchase header
            $purchase->update([
                'vendor_id' => $validated['vendor_id'],
                'purchase_date' => $validated['purchase_date'],
                'total_amount' => $validated['total_amount'],
                'user_id' => Auth::User()->id,
            ]);

            //kembalikan stock nya dulu
            foreach($purchase->details as $detail){
                $itemId = $detail->item_id;
                $findItem = Item::find($itemId);
                $findItem->Update([
                    'stock' => $findItem->stock - $detail->quantity,
                ]);
            }
            // Remove old purchase details and add new ones
            $purchase->details()->delete();
            //hapus data di stock_card
            StockCard::where('transaction_number',$purchase->transaction_number)->delete();

            foreach ($validated['items'] as $item) {
                $purchase->details()->create([
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total_price' => $item['quantity'] * $item['price'],
                ]);

                $items = Item::find($item['item_id']);
                $stock_card = StockCard::Create([
                    'item_id'               => $item['item_id'],
                    'transaction_number'    => $purchase->transaction_number,
                    'qty_begin'             => $items->stock,
                    'qty_in'                => $item['quantity'],
                    'qty_out'               => 0,
                    'qty_end'               => $items->stock + $item['quantity']
                ]);

                $items->Update([
                    'stock' => $stock_card->qty_end,
                ]);
            }

            DB::commit();

            return redirect()->route('purchases.index')->with('success', 'Transaksi Pembelian Berhasil Di Ubah.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal Memperbarui Transaksi Pembelian: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan detail pembelian tertentu.
     */
    public function show(PurchaseHeader $purchase)
    {
        $purchase->load('vendor', 'details.item');
        return view('purchases.show', compact('purchase'));
    }

    /**
     * Hapus pembelian.
     */
    public function destroy(PurchaseHeader $purchase)
    {
        try {
            //kembalikan stock nya dulu
            foreach($purchase->details as $detail){
                $itemId = $detail->item_id;
                $findItem = Item::find($itemId);
                $findItem->Update([
                    'stock' => $findItem->stock - $detail->quantity,
                ]);
            }
            //hapus data di stock_card
            StockCard::where('transaction_number',$purchase->transaction_number)->delete();

            $purchase->delete();
            return redirect()->route('purchases.index')->with('success', 'Transaksi Pembelian Berhasil Di Hapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal Menghapus Transaksi Pembelian: ' . $e->getMessage());
        }
    }

    //report
    // Controller method untuk generate report
    public function generateReport(Request $request)
    {
        // Filter berdasarkan tanggal (opsional)
        $fromDate = Carbon::parse($request->input('from_date', Carbon::now()->startOfDay()));
        $toDate = Carbon::parse($request->input('to_date', Carbon::now()->endOfDay()));

        // Tentukan waktu yang lebih presisi
        $fromDate = $fromDate->startOfDay();  // 00:00:00
        $toDate = $toDate->endOfDay();  // 23:59:59

        $perPage = $request->get('per_page', 10); // Default to 10 per page

        // Ambil filter pilihan grup
        $group = $request->input('group', 'vendor'); // Default 'vendor'

        // Default: laporan per vendor
        if ($group == 'vendor') {
            $purchases = PurchaseHeader::select('vendor_id',
                                                DB::raw('SUM(purchase_headers.total_amount) as total_amount'),
                                                DB::raw('SUM(purchase_details.quantity) as total_quantity') // Menjumlahkan quantity dari details
                                            )
                ->join('purchase_details', 'purchase_headers.id', '=', 'purchase_details.purchase_header_id')
                ->with(['vendor','details', 'details.item'])
                ->whereBetween('purchase_headers.created_at', [$fromDate, $toDate])
                ->groupBy('purchase_headers.vendor_id')
                ->paginate($perPage);
        }

        if ($group == 'item') {
            // Query untuk grup berdasarkan item
            $items = Item::select('items.name')
                ->join('purchase_details', 'items.id', '=', 'purchase_details.item_id')
                ->join('purchase_headers', 'purchase_details.purchase_header_id', '=', 'purchase_headers.id')
                ->whereBetween('purchase_headers.created_at', [$fromDate, $toDate])
                ->selectRaw('SUM(purchase_details.quantity) as total_quantity')
                ->selectRaw('SUM(purchase_details.total_price) as total_price')
                ->groupBy('items.name')
                ->having('total_quantity', '>', 0) // Hanya ambil item dengan total quantity > 0
                ->paginate($perPage);

            // Jika tombol export ditekan
            if ($request->has('export') && $request->input('export') == 'excel') {
                $purchases = PurchaseHeader::with(['vendor', 'details.item'])
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->get();

                // Lakukan ekspor ke Excel
                return Excel::download(new PurchaseExport($purchases), 'purchase_report.xlsx');
            }

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('purchases.reports.partials.item_table', compact('items', 'fromDate', 'toDate', 'group', 'perPage'))->render()
                ]);
            }

            return view('purchases.reports.report', compact('items', 'fromDate', 'toDate', 'group', 'perPage'));
        }


        // Jika tombol export ditekan
        if ($request->has('export') && $request->input('export') == 'excel') {
            $purchases = PurchaseHeader::with(['vendor', 'details.item'])
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->get();
            // Lakukan ekspor ke Excel
            return Excel::download(new PurchaseExport($purchases), 'purchase_report.xlsx');
        }

        if ($request->ajax()) {
            return response()->json([
                'html' => view('purchases.reports.partials.customer_table', compact('purchases', 'fromDate', 'toDate', 'group', 'perPage'))->render()
            ]);
        }

        return view('purchases.reports.report', compact('purchases', 'fromDate', 'toDate', 'group', 'perPage'));
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

        // Ambil data vendor berdasarkan rentang tanggal
        $PurchaseQuery = PurchaseHeader::select('vendor_id',
                                            DB::raw('SUM(purchase_headers.total_amount) as total_amount'),
                                            DB::raw('SUM(purchase_details.quantity) as total_quantity') // Menjumlahkan quantity dari details
                                        )
                                    ->join('purchase_details', 'purchase_headers.id', '=', 'purchase_details.purchase_header_id')
                                    ->with(['vendor','details', 'details.item'])
                                    ->whereBetween('purchase_headers.created_at', [$fromDate, $toDate])
                                    ->groupBy('purchase_headers.vendor_id');

        // Jika memilih per customer
        if ($group == 'vendor') {
            $vendors = $PurchaseQuery->get(); // Ambil semua vendor per customer
            $view = 'purchases.reports.report_vendors_pdf';
            $data = compact('vendors', 'fromDate', 'toDate');

        }

        // Jika memilih per item
        if ($group == 'item') {
            // Grouping berdasarkan item
            $items = Item::with(['purchaseDetails' => function($query) use ($fromDate, $toDate) {
                $query->whereHas('purchaseHeader', function($q) use ($fromDate, $toDate) {
                    $q->whereBetween('created_at', [$fromDate, $toDate]);
                });
            }])
            ->get()
            ->map(function($item) {
                // Hitung total quantity dan total sales per item
                $totalQuantity = $item->purchaseDetails->sum('quantity');
                $total_price = $item->purchaseDetails->sum(function($detail) {
                    return $detail->total_price;
                });

                // Hanya kembalikan item yang memiliki total quantity lebih dari 0
                if ($totalQuantity > 0) {
                    return (object)[
                        'name' => $item->name,
                        'total_quantity' => $totalQuantity,
                        'total_price' => $total_price
                    ];
                }

                // Jika quantity tidak lebih dari 0, kembalikan null
                return null;
            })
            ->filter() // Filter item yang bernilai null (yaitu item dengan quantity 0)
            ->values(); // Reindex array setelah filter

            $view = 'purchases.reports.report_items_pdf';
            $data = compact('items', 'fromDate', 'toDate');
        }

        // Buat PDF dari tampilan yang sesuai
        $pdf = Pdf::loadView($view, $data);

        // Return PDF untuk diunduh atau ditampilkan
        return $pdf->stream('Purchases_Report_' . now()->format('Ymd') . '.pdf');
    }
}
