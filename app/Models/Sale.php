<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    // Kolom yang dapat diisi secara mass-assignment
    protected $fillable = [
        'customer_id',
        'total_price',
        'discount',
        'transaction_number',
        'user_id',
        'payment_amount',
        'change_amount',
        'type_id',
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
        $prefix = 'TRX-';
        $date = now()->format('Ymd');
        $lastSale = self::whereDate('created_at', now()->toDateString())
                        ->orderBy('id', 'desc')
                        ->first();

        $lastNumber = $lastSale ? intval(substr($lastSale->transaction_number, -4)) : 0;

        return $prefix . $date . '-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Menghitung total diskon yang ada di header dan detail.
     */
    public function calculateTotalDiscount()
    {
        // Diskon dari header
        $headerDiscount = $this->discount;

        // Diskon dari detail
        $detailDiscount = $this->details->sum('discount');

        return $headerDiscount + $detailDiscount;
    }

    /**
     * Menghitung total harga setelah diskon.
     */
    public function calculateTotalPrice()
    {
        // Total harga sebelum diskon
        $totalPrice = $this->details->sum('subtotal');

        // Diskon total (header + detail)
        $totalDiscount = $this->calculateTotalDiscount();

        // Total harga setelah diskon
        return $totalPrice - $totalDiscount;
    }

    /**
     * Relasi ke model Customer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relasi ke model SalesDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details()
    {
        return $this->hasMany(SalesDetail::class, 'sales_id');
    }

    // Relasi dengan User (transaksi dibuat oleh user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function type()
    {
        return $this->belongsTo(Type::class);
    }
}
