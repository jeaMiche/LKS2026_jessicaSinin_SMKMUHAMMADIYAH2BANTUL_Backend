<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;


class DashboardController extends Controller
{
    public function index(): Response
    {
        /** @var User $user */
        $user = auth()->user();

        return Inertia::render('Dashboard', [
            'userRole' => $user->role,
            'stats'    => [
                'total'    => \App\Models\Installment::count(),
                'pending'  => \App\Models\Installment::where('status', 'pending')->count(),
                'approved' => \App\Models\Installment::where('status', 'approved')->count(),
            ],
        ]);
    }
}