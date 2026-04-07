<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancingApplication extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'business_verification_id',  
        'jumlah_pembiayaan',         
        'tenor_bulan',              
        'tujuan_pembiayaan',         
        'skor_kelayakan',
        'rekomendasi_limit',
        'catatan_analisis',
        'status',
        'submitted_at',
        'approved_at',
        'rejected_reason',
       
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_at'  => 'datetime',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function businessVerification()
    {
        return $this->belongsTo(BusinessVerification::class);
    }

    public function installments()
    {
        return $this->hasMany(Installment::class);
    }

    public function applicationLogs()
    {
        return $this->hasMany(ApplicationLog::class);
    }


    public function calculateMonthlyInstallment(): int
    {
        $pokok = $this->jumlah_pembiayaan;
        $tenor = $this->tenor_bulan;

        $bunga      = $pokok * 0.06 * ($tenor / 12);
        $total      = $pokok + $bunga;
        $perBulan   = intval($total / $tenor);

        return $perBulan;
    }

    public function generateInstallments(): void
    {
        $cicilan   = $this->calculateMonthlyInstallment();
        $pokok     = intval($this->jumlah_pembiayaan / $this->tenor_bulan);
        $bunga     = $cicilan - $pokok;
        $jatuhTempo = now();

        for ($i = 1; $i <= $this->tenor_bulan; $i++) {
            $jatuhTempo = $jatuhTempo->copy()->addDays(30);

            $this->installments()->create([
                'installment_number' => $i,
                'jatuh_tempo'        => $jatuhTempo->toDateString(),
                'nominal_pokok'      => $pokok,
                'nominal_bunga'      => $bunga,
                'total_cicilan'      => $cicilan,
                'status'             => 'unpaid',
            ]);
        }
    }


    protected static function booted(): void
    {
        static::updating(function (FinancingApplication $app) {
            if ($app->isDirty('status')) {
                ApplicationLog::create([
                    'financing_application_id' => $app->id,
                    'status_from'              => $app->getOriginal('status'),
                    'status_to'                => $app->status,
                    'role'                     => auth()->user()?->role,
                    'user_id'                  => auth()->id(),
                ]);
            }
        });
    }


    public static function hasActiveApplication(string $userId): bool
    {
        return self::where('user_id', $userId)
            ->whereIn('status', [
                'submitted',
                'under_review',
                'recommended',
            ])
            ->exists();
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}