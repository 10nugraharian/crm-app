<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Quotation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'lead_id',
        'sales_id',
        'no_quotation',
        'total_amount',
        'status_approval',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function sales()
    {
        return $this->belongsTo(User::class, 'sales_id');
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class, 'quotation_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'quotation_id');
    }
}
