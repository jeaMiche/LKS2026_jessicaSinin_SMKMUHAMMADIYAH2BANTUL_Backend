<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Installment extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'financing_application_id',
        'amount',
        'due_date',   
        'paid_at',     
        'status',       
        'payment_method' 
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function financingApplication()
    {
        return $this->belongsTo(FinancingApplication::class);
    }
}