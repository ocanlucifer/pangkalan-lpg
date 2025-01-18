<?php

// app/Models/Item.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category_id', 'type_id', 'price', 'stock','active', 'user_id',];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    // Relasi ke SalesDetail
    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class); // Menghubungkan dengan SalesDetail
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

