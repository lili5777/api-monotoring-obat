<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_obat',
        'id_rak',
        'jumlah',
        'masuk',
        'kadaluarsa'
    ];

    public function rak()
    {
        return $this->belongsTo(Rak::class, 'id_rak');
    }
    public function obat()
    {
        return $this->belongsTo(Obat::class, 'id_obat');
    }
}
