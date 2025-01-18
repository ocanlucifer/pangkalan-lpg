<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchaseExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $purchases;

    public function __construct($purchases)
    {
        $this->purchases = $purchases;
    }

    public function collection()
    {
        $data = [];

        foreach ($this->purchases as $purchase) {
            foreach ($purchase->details as $detail) {
                $data[] = [
                    'Nomor Transaksi' => $purchase->transaction_number,
                    'Supplier' => $purchase->vendor->name,
                    'Nama Barang' => $detail->item->name,
                    'Harga' => $detail->price,
                    'Quantity' => $detail->quantity,
                    'Subtotal' => $detail->total_price,
                    'Total Harga' => $purchase->total_amount,
                    'Tanggal' => $purchase->created_at->format('d-m-Y'),
                ];
            }
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Nomor Transaksi', 'Supplier', 'Nama Barang', 'Harga', 'Quantity', 'Subtotal', 'Total Harga', 'Tanggal',
        ];
    }
}
