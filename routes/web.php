<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/checkout', \App\Livewire\Checkout::class)->name('checkout');
Route::get('/pedido-criado/{order_id}', \App\Livewire\Result::class)->middleware(['signed'])->name('checkout.result');
Route::get('/login', \App\Livewire\Login::class)->middleware(['guest'])->name('login');
Route::get('/login/{email}', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'login'])->middleware(['signed'])->name('login.store');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


