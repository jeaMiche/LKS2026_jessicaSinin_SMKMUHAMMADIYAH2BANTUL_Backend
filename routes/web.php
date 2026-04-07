<?php
 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
return view('welcome');
})->name('welcome');

Route::middleware('guest')->group(function () {
Route::get('login', function () {
return view('auth/login');
})->name('login');

Route::post('login', [LoginController::class, 'store']);

Route::get('register', function () {
return view('auth/register');
})->name('register');
});

Route::middleware('auth')->group(function () {
Route::get('/dashboard', function () {
return view('dashboard');
})->name('dashboard');

Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
});