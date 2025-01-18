<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issuing extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan
    protected $table = 'issuings';

    // Kolom yang dapat diisi (mass assignable)
    protected $fillable = [
        'transaction_number',
        'transaction_date',
        'user_id',
        'remarks',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sale) {
            $sale->transaction_number = self::generateTransactionNumber();
        });
    }

    /**
     * Generate a unique transaction number.
     */
    public static function generateTransactionNumber()
    {
        $prefix = 'OUT-';
        $date = now()->format('Ymd');
        $lastSale = self::whereDate('created_at', now()->toDateString())
                        ->orderBy('id', 'desc')
                        ->first();

        $lastNumber = $lastSale ? intval(substr($lastSale->transaction_number, -4)) : 0;

        return $prefix . $date . '-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }

    // Relasi dengan IssuingDetail (satu transaksi bisa punya banyak item)
    public function issuingDetails()
    {
        return $this->hasMany(IssuingDetail::class, 'issuing_id');
    }

    // Relasi dengan User (transaksi dibuat oleh user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'transaction_date' => 'datetime',
    ];
}
