<?php

// app/Models/Type.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;

    protected $fillable = ['name','user_id',];

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    // Relasi dengan User (transaksi dibuat oleh user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

