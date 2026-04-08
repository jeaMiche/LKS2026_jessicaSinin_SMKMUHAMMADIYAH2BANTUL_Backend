<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FinancingController;

Route::post('/login', function (Request $request) {
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json([
            'success' => false,
            'message' => 'Email atau password salah.',
        ], 401);
    }

    $user  = User::where('email', $request->email)->first();
    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'success' => true,
        'token'   => $token,
        'user'    => [
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->role,
        ],
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/installments',                  [FinancingController::class, 'index']);
    Route::post('/installments',                 [FinancingController::class, 'store']);
    Route::patch('/installments/{installment}/review',  [FinancingController::class, 'review']);
    Route::patch('/installments/{installment}/approve', [FinancingController::class, 'approve']);
    Route::patch('/installments/{installment}/reject',  [FinancingController::class, 'reject']);
    Route::patch('/installments/{installment}/disburse',[FinancingController::class, 'disburse']);
    Route::post('/installments/{installment}/pay',      [FinancingController::class, 'pay']);
});