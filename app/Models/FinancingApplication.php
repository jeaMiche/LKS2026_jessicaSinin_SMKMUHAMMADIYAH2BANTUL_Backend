<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancingApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'amount', 'tenor_months', 'interest_rate', 'omzet', 
        'purpose', 'status', 'approved_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function logs()
    {
        return $this->hasMany(ApplicationLog::class);
    }

    public function verification()
    {
        return $this->hasOne(BusinessVerification::class);
    }


    public function installments()
    {
        return $this->hasMany(Installment::class);
    }
}