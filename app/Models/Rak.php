<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rak extends Model
{
    use HasFactory;
    protected $fillable = [
        'nama_rak',
        'kapasitas',
        'terisi',
        'kosong'
    ];

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'id_rak');
    }
}
