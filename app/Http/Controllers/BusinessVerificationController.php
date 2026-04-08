<?php

namespace App\Http\Controllers;

use App\Models\FinancingApplication;
use App\Models\BusinessVerification;
use App\Models\ApplicationLog;
use Illuminate\Http\Request;

class BusinessVerificationController extends Controller
{
    public function verify(Request $request, FinancingApplication $application)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected',
            'notes'  => 'nullable|string'
        ]);

        // Simpan data verifikasi
        BusinessVerification::create([
            'financing_application_id' => $application->id,
            'verified_by'              => auth()->id(),
            'status'                   => $request->status,
            'notes'                    => $request->notes,
        ]);

        // Update status aplikasi & catat log
        $oldStatus = $application->status;
        $application->update(['status' => $request->status]);

        ApplicationLog::create([
            'financing_application_id' => $application->id,
            'status_before'            => $oldStatus,
            'status_after'             => $request->status,
            'note'                     => 'Verifikasi Bisnis: ' . $request->notes,
            'created_by'               => auth()->id(),
        ]);

        return back()->with('success', 'Verifikasi berhasil diproses.');
    }
}