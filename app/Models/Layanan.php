<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Layanan extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'nama_layanan',
        'harga_modal',
        'harga_pokok',
        'komisi_sales',
        'komisi_sso',
    ];

    public function items()
    {
        return $this->hasMany(QuotationItem::class, 'layanan_id');
    }
}
