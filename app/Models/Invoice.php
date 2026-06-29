<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Invoice extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'quotation_id',
        'total_amount',
        'persentase_dp',
        'status_pembayaran',
        'status_approval',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id');
    }

    public function project()
    {
        return $this->hasOne(Project::class, 'invoice_id');
    }

    public function komisis()
    {
        return $this->hasMany(Komisi::class, 'invoice_id');
    }
}
