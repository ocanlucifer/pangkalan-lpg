<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesExport implements FromCollection, WithHeadings
{
    protected $sales;

    public function __construct($sales)
    {
        $this->sales = $sales;
    }

    public function collection()
    {
        $data = [];

        foreach ($this->sales as $sale) {
            foreach ($sale->details as $detail) {
                $data[] = [
                    'Nomor Transaksi' => $sale->transaction_number,
                    'Pelanggan' => $sale->customer->name,
                    'Nama Menu' => $detail->menuItem->name,
                    'Quantity' => $detail->quantity,
                    'Harga' => $detail->price,
                    'Diskon' => $detail->discount,
                    'Subtotal' => $detail->subtotal,
                    'Total Harga' => $sale->total_price,
                    'Total Diskon' => $sale->discount,
                    'Total Setelah Diskon' => $sale->total_price - $sale->discount,
                    'Tanggal' => $sale->created_at->format('d-m-Y'),
                ];
            }
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Nomor Transaksi', 'Pelanggan', 'Nama Menu', 'Quantity', 'Harga', 'Diskon', 'Subtotal', 'Total Harga', 'Total Diskon', 'Total Setelah Diskon', 'Tanggal',
        ];
    }
}
