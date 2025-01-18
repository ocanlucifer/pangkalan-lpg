<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseHeader extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_number',
        'vendor_id',
        'purchase_date',
        'total_amount',
        'user_id',
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
        $prefix = 'PURCH-';
        $date = now()->format('Ymd');
        $lastSale = self::whereDate('created_at', now()->toDateString())
                        ->orderBy('id', 'desc')
                        ->first();

        $lastNumber = $lastSale ? intval(substr($lastSale->transaction_number, -4)) : 0;

        return $prefix . $date . '-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }

    // Relasi dengan PurchaseDetail (1 transaksi bisa memiliki banyak detail)
    public function details()
    {
        return $this->hasMany(PurchaseDetail::class, 'purchase_header_id');
    }

    // Relasi dengan Vendor
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    // Relasi dengan User (transaksi dibuat oleh user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'purchase_date' => 'datetime',
    ];
}
