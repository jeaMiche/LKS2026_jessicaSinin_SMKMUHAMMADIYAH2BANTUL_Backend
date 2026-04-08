<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessVerification extends Model
{
    protected $fillable = [
        'financing_application_id', 'verified_by', 
        'status', 'notes'
    ];

    public function application()
    {
        return $this->belongsTo(FinancingApplication::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}