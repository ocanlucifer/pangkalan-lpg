<?php

// app/Models/Customer.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address', 'contact', 'active', 'user_id',];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    // Relasi dengan User (transaksi dibuat oleh user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
