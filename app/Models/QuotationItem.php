<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class QuotationItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'quotation_id',
        'layanan_id',
        'qty',
        'harga_jual_input',
        'refund_sales_margin',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id');
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }
}
