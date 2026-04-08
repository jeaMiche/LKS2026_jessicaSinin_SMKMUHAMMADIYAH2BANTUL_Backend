<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use Illuminate\Http\Request;

class InstallmentController extends Controller
{
    public function pay(Request $request, Installment $installment)
    {
        // Validasi pembayaran cicilan
        $installment->update([
            'status'       => 'paid',
            'paid_at'      => now(),
            'payment_method' => $request->method ?? 'transfer',
        ]);

        // Opsional: Cek apakah semua cicilan sudah lunas untuk update status FinancingApplication
        $application = $installment->financingApplication;
        $remaining = $application->installments()->where('status', 'unpaid')->count();

        if ($remaining === 0) {
            $application->update(['status' => 'lunas']);
        }

        return back()->with('success', 'Cicilan berhasil dibayar.');
    }
}