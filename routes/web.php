<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ForgotPasswordController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');

