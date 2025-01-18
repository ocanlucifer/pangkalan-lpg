<?php

namespace App\Http\Controllers;

use App\Exports\IssuingExport;
use App\Models\Issuing;
use App\Models\IssuingDetail;
use App\Models\Item;
use App\Models\StockCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class IssuingController extends Controller
{
    /**
     * Tampilkan daftar transaksi pengeluaran barang.
     */
    public function index(Request $request)
    {
        $sortBy = $request->get('sort_by', 'transaction_number');  // Default sorting berdasarkan transaction_number
        $order = $request->get('order', 'asc');  // Default ascending
        $search = $request->get('search', '');
        $perPage = $request->get('per_page', 10); // Default to 10 per page
        $fromDate = $request->input('from_date', now()->toDateString());
        $toDate = $request->input('to_date', now()->toDateString());

        $issuings = Issuing::query()
                    ->when($search, function ($query, $search) {
                        return $query->where('transaction_number', 'like', "%$search%");
                    })
                    ->with(['user', 'issuingDetails.item']);

        // Handle sorting logic
        $issuings->orderBy($sortBy, $order);

        if ($fromDate && $toDate) {
            $issuings->whereBetween('created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ]);
        } elseif ($fromDate) {
            $issuings->whereDate('created_at', '>=', $fromDate);
        } elseif ($toDate) {
            $issuings->whereDate('created_at', '<=', $toDate);
        }

        $result = $issuings->paginate($perPage);

        // If the request is an AJAX request, return the partial view with the table
        if ($request->ajax()) {
            return view('issuings.table', compact('result', 'search', 'perPage', 'sortBy', 'order', 'fromDate', 'toDate'));
        }

        return view('issuings.index', compact('result', 'search', 'perPage', 'sortBy', 'order', 'fromDate', 'toDate'));
    }

    /**
     * Form untuk membuat transaksi pengeluaran barang baru.
     */
    public function create()
    {
        $items = Item::where('Active',1)->get();
        return view('issuings.create', compact('items'));
    }

    /**
     * Simpan transaksi pengeluaran barang baru.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'transaction_date' => 'required|date',
            'remarks' => 'nullable|string',
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $errors = [];  // Menyimpan semua error yang ditemukan

        DB::beginTransaction();

        try {
            // Create issuing header
            $issuing = Issuing::create([
                'transaction_date' => $validatedData['transaction_date'],
                'user_id' => Auth::User()->id,
                'remarks' => $validatedData['remarks'],
            ]);

            // Add issuing details
            foreach ($validatedData['items'] as $item) {
                $itemModel = Item::find($item['item_id']);

                // Check if the requested quantity is greater than the available stock
                if ($item['quantity'] > $itemModel->stock) {
                    // Simpan error dalam array untuk setiap item
                    $errors[] = "Stok untuk barang: {$itemModel->name} Tidak Cukup. Stok yang tersedia: {$itemModel->stock}, Sedangkan yang anda minta: {$item['quantity']}.";
                } else {
                    $issuing->issuingDetails()->create([
                        'item_id' => $item['item_id'],
                        'quantity' => $item['quantity'],
                    ]);
                    // stock_card
                    $stock_card = StockCard::Create([
                        'item_id'               => $item['item_id'],
                        'transaction_number'    => $issuing->transaction_number,
                        'qty_begin'             => $itemModel->stock,
                        'qty_in'                => 0,
                        'qty_out'               => $item['quantity'],
                        'qty_end'               => $itemModel->stock - $item['quantity']
                    ]);

                    // Update item stock
                    $itemModel->Update([
                        'stock' => $stock_card->qty_end,
                    ]);
                }
            }
            if (!empty($errors)) {
                // Jika ada error, roll back dan kirim semua error yang ditemukan
                DB::rollBack();
                return back()->with('error', implode('<br>', $errors));  // Menampilkan semua error dalam satu pesan
            }

            DB::commit();

            return redirect()->route('issuings.index')->with('success', 'Transaksi Pengeluaran Baranga Berhasil Di Buat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal Membuat Transaksi Pengeluaran Barang: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan form untuk mengedit transaksi pengeluaran barang.
     */
    public function edit(Issuing $issuing)
    {
        $items = Item::where('Active',1)->get();
        return view('issuings.edit', compact('issuing', 'items'));
    }

    /**
     * Update transaksi pengeluaran barang.
     */
    public function update(Request $request, Issuing $issuing)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'remarks' => 'nullable|string',
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $errors = [];  // Menyimpan semua error yang ditemukan

        DB::beginTransaction();

        try {
            // Update issuing header
            $issuing->update([
                'transaction_date' => $validated['transaction_date'],
                'user_id' => Auth::User()->id,
                'remarks' => $validated['remarks'],
            ]);

            foreach ($validated['items'] as $item) {
                $itm = Item::find($item['item_id']);
                // Check if the requested quantity is greater than the available stock
                if ($item['quantity'] > $itm->stock) {
                    // Simpan error dalam array untuk setiap item
                    $errors[] = "Stok untuk barang: {$itm->name} tidak cukup. stok yang tersedia: {$itm->stock}, sedangkan yang anda minta: {$item['quantity']}.";
                }
            }

            if (!empty($errors)) {
                // Jika ada error, roll back dan kirim semua error yang ditemukan
                DB::rollBack();
                return back()->with('error', implode('<br>', $errors));  // Menampilkan semua error dalam satu pesan
            }

            //kembalikan stock nya dulu
            foreach ($issuing->issuingDetails as $detail) {
                $item = Item::find($detail->item_id);
                $item->update(['stock' => $item->stock + $detail->quantity]);  // Restore stock before deleting
            }

            // Hapus old issuing details
            $issuing->issuingDetails()->delete();

            //hapus data di stock_card
            StockCard::where('transaction_number',$issuing->transaction_number)->delete();

            // Add issuing details
            foreach ($validated['items'] as $item) {
                $itemModel = Item::find($item['item_id']);

                $issuing->issuingDetails()->create([
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                ]);
                // stock_card
                $stock_card = StockCard::Create([
                    'item_id'               => $item['item_id'],
                    'transaction_number'    => $issuing->transaction_number,
                    'qty_begin'             => $itemModel->stock,
                    'qty_in'                => 0,
                    'qty_out'               => $item['quantity'],
                    'qty_end'               => $itemModel->stock - $item['quantity']
                ]);

                // Update item stock
                $itemModel->Update([
                    'stock' => $stock_card->qty_end,
                ]);
            }

            DB::commit();

            return redirect()->route('issuings.index')->with('success', 'Transaksi Pengeluaran Barang Berhasil Di Ubah.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'gagal Menyimpan Perubahan Transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan detail transaksi pengeluaran barang tertentu.
     */
    public function show(Issuing $issuing)
    {
        $issuing->load('user', 'issuingDetails.item');
        return view('issuings.show', compact('issuing'));
    }

    /**
     * Hapus transaksi pengeluaran barang.
     */
    public function destroy(Issuing $issuing)
    {
        DB::beginTransaction();

        try {
            // Restore stock before delete
            foreach ($issuing->issuingDetails as $detail) {
                $item = Item::find($detail->item_id);
                $item->update(['stock' => $item->stock + $detail->quantity]);
            }

            //hapus data di stock_card
            StockCard::where('transaction_number',$issuing->transaction_number)->delete();

            // Delete issuing details
            $issuing->issuingDetails()->delete();
            // Delete the issuing transaction
            $issuing->delete();

            DB::commit();

            return redirect()->route('issuings.index')->with('success', 'Transaksi Pengeluaran Barang Berhasil Di Hapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal Menghapus Transaksi: ' . $e->getMessage());
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

        // Query untuk grup berdasarkan item
        $items = Item::select('items.name')
            ->join('issuing_detail', 'items.id', '=', 'issuing_detail.item_id')
            ->join('issuings', 'issuing_detail.issuing_id', '=', 'issuings.id')
            ->whereBetween('issuings.created_at', [$fromDate, $toDate])
            ->selectRaw('SUM(issuing_detail.quantity) as total_quantity')
            ->groupBy('items.name')
            ->having('total_quantity', '>', 0) // Hanya ambil item dengan total quantity > 0
            ->paginate($perPage);


        // Jika tombol export ditekan
        if ($request->has('export') && $request->input('export') == 'excel') {
            // Lakukan ekspor ke Excel
            return Excel::download(new IssuingExport($items), 'purchase_report.xlsx');
        }

        if ($request->ajax()) {
            return response()->json([
                'html' => view('issuings.reports.table', compact('items', 'fromDate', 'toDate', 'perPage'))->render()
            ]);
        }

        return view('issuings.reports.report', compact('items', 'fromDate', 'toDate', 'perPage'));
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

        // Query untuk grup berdasarkan item
        $items = Item::select('items.name')
            ->join('issuing_detail', 'items.id', '=', 'issuing_detail.item_id')
            ->join('issuings', 'issuing_detail.issuing_id', '=', 'issuings.id')
            ->whereBetween('issuings.created_at', [$fromDate, $toDate])
            ->selectRaw('SUM(issuing_detail.quantity) as total_quantity')
            ->groupBy('items.name')
            ->having('total_quantity', '>', 0) // Hanya ambil item dengan total quantity > 0
            ->get();

        $view = 'issuings.reports.report_items_pdf';
        $data = compact('items', 'fromDate', 'toDate');

        // Buat PDF dari tampilan yang sesuai
        $pdf = Pdf::loadView($view, $data);

        // Return PDF untuk diunduh atau ditampilkan
        return $pdf->stream('Issuings_Report_' . now()->format('Ymd') . '.pdf');
    }

}
