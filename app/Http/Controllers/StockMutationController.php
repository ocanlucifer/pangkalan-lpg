<?php

namespace App\Http\Controllers;

use App\Exports\MutationExport;
use App\Models\StockCard;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class StockMutationController extends Controller
{
    public function index(Request $request)
    {
        // Filter berdasarkan tanggal (opsional)
        $fromDate = Carbon::parse($request->input('from_date', Carbon::now()->startOfDay()));
        $toDate = Carbon::parse($request->input('to_date', Carbon::now()->endOfDay()));

        // Tentukan waktu yang lebih presisi
        $fromDate = $fromDate->startOfDay();  // 00:00:00
        $toDate = $toDate->endOfDay();  // 23:59:59


        $perPage = $request->get('per_page', 10); // Default to 10 per page

        $stockMutations = Item::select([
            'items.id AS item_id',
            'items.name',
            DB::raw('(SELECT qty_begin
                      FROM stock_cards AS X
                      WHERE X.item_id = items.id
                        AND X.created_at = (
                            SELECT MIN(created_at)
                            FROM stock_cards AS Y
                            WHERE Y.item_id = items.id
                              AND Y.created_at BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                        ) ORDER BY X.created_at ASC LIMIT 1
                     ) AS qty_begin'),
            DB::raw('COALESCE(SUM(stock_cards.qty_in), 0) AS qty_in'),
            DB::raw('COALESCE(SUM(stock_cards.qty_out), 0) AS qty_out'),
            DB::raw('(SELECT qty_end
                      FROM stock_cards AS Z
                      WHERE Z.item_id = items.id
                        AND Z.created_at = (
                            SELECT MAX(created_at)
                            FROM stock_cards AS O
                            WHERE O.item_id = items.id
                              AND O.created_at BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                        ) ORDER BY Z.created_at DESC LIMIT 1
                     ) AS qty_end'),
        ])
        ->leftJoin('stock_cards', 'items.id', '=', 'stock_cards.item_id') // Join stock_cards
        ->whereBetween('stock_cards.created_at', [$fromDate, $toDate]) // Filter berdasarkan tanggal
        ->groupBy('items.id', 'items.name') // Group berdasarkan item ID dan nama
        ->paginate($perPage); // Pagination


        // Jika tombol export ditekan
        if ($request->has('export') && $request->input('export') == 'excel') {
            // Lakukan ekspor ke Excel
            return Excel::download(new MutationExport($stockMutations), 'stock_mutation_report.xlsx');
        }

        // Jika request adalah AJAX, kirimkan data tabel saja
        if ($request->ajax()) {
            return view('stock-mutations.table', compact('stockMutations', 'fromDate', 'toDate', 'perPage'))->render();
        }

        return view('stock-mutations.index', compact('stockMutations', 'fromDate', 'toDate', 'perPage'));
    }

    public function printReportPDF(Request $request)
    {
        // Filter berdasarkan tanggal (opsional)
        $fromDate = Carbon::parse($request->input('from_date', Carbon::now()->startOfDay()));
        $toDate = Carbon::parse($request->input('to_date', Carbon::now()->endOfDay()));

        // Tentukan waktu yang lebih presisi
        $fromDate = $fromDate->startOfDay();  // 00:00:00
        $toDate = $toDate->endOfDay();  // 23:59:59

        // Query menggunakan Eloquent
        $stockMutations = StockCard::select([
            'stock_cards.item_id',
            'items.name',
            DB::raw('(SELECT qty_begin
                    FROM stock_cards AS X
                    WHERE X.item_id = stock_cards.item_id
                        AND X.created_at = (
                            SELECT MIN(created_at)
                            FROM stock_cards AS Y
                            WHERE Y.item_id = stock_cards.item_id
                              AND Y.created_at BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                        ) ORDER BY X.created_at ASC LIMIT 1
                    ) AS qty_begin'),
            DB::raw('SUM(stock_cards.qty_in) AS qty_in'),
            DB::raw('SUM(stock_cards.qty_out) AS qty_out'),
            DB::raw('(SELECT qty_end
                    FROM stock_cards AS Z
                    WHERE Z.item_id = stock_cards.item_id
                        AND Z.created_at = (
                            SELECT MAX(created_at)
                            FROM stock_cards AS O
                            WHERE O.item_id = stock_cards.item_id
                              AND O.created_at BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                        ) ORDER BY Z.created_at DESC LIMIT 1
                    ) AS qty_end')
        ])
        ->leftjoin('items', 'stock_cards.item_id', '=', 'items.id')
        ->whereBetween('stock_cards.created_at', [$fromDate, $toDate]) // Filter by date
        ->groupBy('stock_cards.item_id', 'items.name')
        ->get();

        $view = 'stock-mutations.report_stock_mutation_pdf';
        $data = compact('stockMutations', 'fromDate', 'toDate');

        // Buat PDF dari tampilan yang sesuai
        $pdf = Pdf::loadView($view, $data);

        // Return PDF untuk diunduh atau ditampilkan
        return $pdf->stream('StockMutation_Report_' . now()->format('Ymd') . '.pdf');
    }
}
