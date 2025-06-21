<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    use HasFactory;
    protected $fillable = [
        'kode',
        'nama_obat',
    ];

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'id_obat');
    }
}
