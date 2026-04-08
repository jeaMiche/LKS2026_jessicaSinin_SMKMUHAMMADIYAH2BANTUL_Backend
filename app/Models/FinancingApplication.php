<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancingApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'business_verification_id',
        'omzet',
        'jumlah_pembiayaan',
        'tenor_bulan',
        'tujuan_pembiayaan', 
        'status',
        'skor_kelayakan',
        'rekomendasi_limit',
        'catatan_analisis',
        'submitted_at',
        'approved_at',
        'rejected_reason'
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