<?php

// app/Models/Vendor.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address', 'contact', 'active','user_id',];

    // Optionally, you can define a default scope to query only active vendors by default
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function purchases()
    {
        return $this->hasMany(PurchaseHeader::class);
    }

    // Relasi dengan User (transaksi dibuat oleh user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
