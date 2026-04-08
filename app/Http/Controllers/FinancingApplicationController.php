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
            'name' => 'required|string|max:255',
            'business_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1000000',
            'tenor_months' => 'required|integer',
            'purpose' => 'required|string',
            'omzet' => 'required|numeric|min:1',
        ]);


        $rate = 6;
        $totalInterest = ($request->amount * $rate / 100) * ($request->tenor_months / 12);
        $totalPayable = $request->amount + $totalInterest;
        $monthly = round($totalPayable / $request->tenor_months);

        $app = FinancingApplication::create([
            'id' => Str::uuid(),
            'name' => $request->name,
            'business_name' => $request->business_name,
            'amount' => $request->amount,
            'omzet' => $request->omzet,
            'tenor_months' => $request->tenor_months,
            'interest_rate' => $rate,
            'monthly_installment' => $monthly,
            'outstanding_balance' => $totalPayable,
            'purpose' => $request->purpose,
            'status' => 'pending'
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