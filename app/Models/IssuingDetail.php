<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssuingDetail extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan
    protected $table = 'issuing_detail';

    // Kolom yang dapat diisi (mass assignable)
    protected $fillable = [
        'issuing_id',
        'item_id',
        'quantity',
    ];

    // Relasi dengan Issuing (setiap detail terkait dengan satu transaksi)
    public function issuing()
    {
        return $this->belongsTo(Issuing::class, 'issuing_id');
    }

    // Relasi dengan Item (setiap detail terkait dengan satu item)
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
