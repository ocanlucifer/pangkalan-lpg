<?php

// app/Models/Item.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'buy_price', 'sell_price', 'stock','active', 'user_id',];

    // Relasi ke SalesDetail
    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class); // Menghubungkan dengan SalesDetail
    }
    // Relasi ke SalesDetail
    public function salesDetails()
    {
        return $this->hasMany(SalesDetail::class); // Menghubungkan dengan SalesDetail
    }


    // Relasi dengan User (transaksi dibuat oleh user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function scopeIsItem($query)
    // {
    //     return $query->where('type_id', 2);
    // }

    // public function scopeIsMenu($query)
    // {
    //     return $query->where('type_id','<>', 2);
    // }

}

