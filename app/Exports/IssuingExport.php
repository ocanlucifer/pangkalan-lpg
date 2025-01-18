<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class IssuingExport implements FromCollection, WithHeadings
{
    protected $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function collection()
    {
        $data = [];

        foreach ($this->items as $item) {
                $data[] = [
                    'Nama Barang' => $item->name,
                    'Total Qty' => $item->total_quantity
                ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Nama Barang', 'Total Qty',
        ];
    }
}
