<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    protected $fillable = [
        'name',
        'email',
        'password',
        'user_id',
        'role',
        'no_rekening',
        'nama_bank',
        'leader_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function subordinates()
    {
        return $this->hasMany(User::class, 'leader_id');
    }

    public function salesLeads()
    {
        return $this->hasMany(Lead::class, 'sales_id');
    }

    public function ssoLeads()
    {
        return $this->hasMany(Lead::class, 'sso_id');
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class, 'sales_id');
    }

    public function komisis()
    {
        return $this->hasMany(Komisi::class, 'sales_id');
    }
}
