<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'role',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'created_at'  => 'datetime',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            
            'password' => 'hashed',
        ];
    }

   
    public function businessVerification()
    {
        return $this->hasOne(BusinessVerification::class);
    }

    public function financingApplications()
    {
        return $this->hasMany(FinancingApplication::class);
    }

    public function applicationLogs()
    {
        return $this->hasMany(ApplicationLog::class);
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isAdmin(): bool      { return $this->role === 'admin'; }
    public function isApplicant(): bool  { return $this->role === 'applicant'; }
    public function isVerifier(): bool   { return $this->role === 'verifier'; }
    public function isAnalyst(): bool    { return $this->role === 'analyst'; }
    public function isManager(): bool    { return $this->role === 'manager'; }
}