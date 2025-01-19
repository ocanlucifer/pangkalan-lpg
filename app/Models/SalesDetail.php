<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesDetail extends Model
{
    use HasFactory;

    // Kolom yang dapat diisi secara mass-assignment
    protected $fillable = ['sales_id', 'item_id', 'quantity', 'price', 'subtotal', 'discount'];

    /**
     * Relasi ke model Sale (header)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sales_id');
    }

    /**
     * Relasi ke model Item
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    // public function item()
    // {
    //     return $this->belongsTo(Item::class);
    // }

    /**
     * Relasi ke MenuItem.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
