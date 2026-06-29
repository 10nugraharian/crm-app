<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Komisi extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'sales_id',
        'invoice_id',
        'total_refund_sales',
        'total_komisi_fix',
        'status_pencairan',
    ];

    public function sales()
    {
        return $this->belongsTo(User::class, 'sales_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
