<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Lead extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'sales_id',
        'sso_id',
        'status_leads',
        'kualifikasi',
        'nama_perusahaan',
        'jenis_perusahaan',
        'tingkat_kualifikasi',
        'sub_klasifikasi',
        'tanggal_expired',
        'nama_pic',
        'alamat',
        'no_telepon',
        'email',
        'wilayah',
    ];

    protected $casts = [
        'wilayah' => 'array',
    ];

    public function sales()
    {
        return $this->belongsTo(User::class, 'sales_id');
    }

    public function sso()
    {
        return $this->belongsTo(User::class, 'sso_id');
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class, 'lead_id');
    }

    public function pengechekanLogs()
    {
        return $this->hasMany(PengechekanLog::class, 'lead_id');
    }
}
