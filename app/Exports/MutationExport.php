<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MutationExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $stockMutations;

    public function __construct($stockMutations)
    {
        $this->stockMutations = $stockMutations;
    }

    public function collection()
    {
        $data = [];

        foreach ($this->stockMutations as $mutation) {
                $data[] = [
                    'Nama Barang' => $mutation->name,
                    'Qty Awal' => $mutation->qty_begin,
                    'Qty Masuk' => $mutation->qty_in,
                    'Qty Keluar' => $mutation->qty_out,
                    'Qty Akhir' => $mutation->qty_end,
                ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Nama Barang', 'Qty Awal', 'Qty Masuk', 'Qty Keluar', 'Qty Akhir',
        ];
    }
}
