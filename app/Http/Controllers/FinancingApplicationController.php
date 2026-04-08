<?php

namespace App\Http\Controllers;

use App\Models\FinancingApplication;
use App\Models\ApplicationLog;
use App\Models\Installment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class FinancingApplicationController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return Inertia::render('Financing/Index', [
            'financings' => FinancingApplication::with('user')->latest()->paginate(10),
            'userRole' => $user->role,
            'canApplyfinancing' => $user->isApplicant(),
            'canReview' => $user->isVerifier(),
            'canApprove' => $user->isManager() || $user->isAnalyst(),
        ]);
    }


    public function adminIndex()
    {
        return Inertia::render('Financing/Index', [
            'applications' => FinancingApplication::latest()->paginate(10),
            'isAdmin' => true
        ]);
    }

    public function create()
    {
        return Inertia::render('Financing/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000000',
            'tenor_months' => 'required|integer',
            'purpose' => 'required|string',
            'omzet' => 'required|numeric',
        ]);

        $user = auth()->user();

        $verification = \App\Models\BusinessVerification::where('user_id', $user->id)->first();

        if (!$verification) {
            return back()->withErrors(['error' => 'Anda harus melakukan verifikasi bisnis terlebih dahulu.']);
        }

        $app = FinancingApplication::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'user_id' => $user->id,
            'business_verification_id' => $verification->id, 
            'omzet' => $request->omzet,
            'jumlah_pembiayaan' => $request->amount,     
            'tenor_bulan' => $request->tenor_months, 
            'tujuan_pembiayaan' => $request->purpose,   
            'status' => 'submitted',         
            'submitted_at' => now(),
        ]);

        return redirect()->route('financing.show', $app->id);
    }

    public function show(FinancingApplication $application)
    {
        $application->load('installments');

        return Inertia::render('Financing/Show', [
            'application' => $application
        ]);
    }

    public function payForm(FinancingApplication $application)
    {
        return Inertia::render('Financing/PaymentForm', [
            'application' => $application
        ]);
    }

    public function payStore(Request $request, FinancingApplication $application)
    {
        $nextInstallment = $application->installments()->where('status', 'unpaid')->first();

        if ($nextInstallment) {
            $nextInstallment->update([
                'status' => 'paid',
                'paid_at' => now(),
                'payment_method' => $request->method
            ]);

            $application->decrement('outstanding_balance', $nextInstallment->amount);

            if ($application->outstanding_balance <= 0) {
                $application->update(['status' => 'lunas']);
            }
        }

        return redirect()->route('financing.show', $application->id);
    }

    public function verify(FinancingApplication $application)
    {
        $application->update(['status' => 'verified']);
        return back();
    }

    public function approve(FinancingApplication $application)
    {
        $application->update(['status' => 'approved']);
        return back();
    }

    public function disburse(FinancingApplication $application)
    {
        $application->update(['status' => 'disbursed']);

        for ($i = 1; $i <= $application->tenor_months; $i++) {
            Installment::create([
                'financing_application_id' => $application->id,
                'amount' => $application->monthly_installment,
                'due_date' => now()->addMonths($i),
                'status' => 'unpaid'
            ]);
        }

        return back();
    }

    public function destroy(FinancingApplication $application)
    {
        $application->delete();
        return redirect()->route('admin.dashboard');
    }
}