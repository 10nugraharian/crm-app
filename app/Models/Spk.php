<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Spk extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'project_id',
        'vendor_id',
        'no_spk',
        'nilai_pekerjaan',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
