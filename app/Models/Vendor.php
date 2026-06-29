<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Vendor extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'nama_vendor',
        'kontak',
    ];

    public function spks()
    {
        return $this->hasMany(Spk::class, 'vendor_id');
    }
}
