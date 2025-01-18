<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_header_id', 'item_id', 'quantity', 'price', 'total_price',
    ];

    // Relasi dengan PurchaseHeader
    public function purchaseHeader()
    {
        return $this->belongsTo(PurchaseHeader::class, 'purchase_header_id');
    }

    // Relasi dengan Item
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
