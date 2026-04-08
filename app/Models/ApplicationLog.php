<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationLog extends Model
{
    protected $fillable = [
        'financing_application_id', 'status_before', 
        'status_after', 'note', 'created_by'
    ];

    public function application()
    {
        return $this->belongsTo(FinancingApplication::class, 'financing_application_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}