<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SocialLoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::controller(SocialLoginController::class)->group(function(){
    Route::get('/auth/google',  'redirectToGoogle')->name('auth.google');
    Route::get('/auth/google-callback', 'handleGoogleCallback');
    Route::get('/auth/github',  'redirectToGithub')->name('auth.github');
    Route::get('/auth/github/callback', 'handleGithubCallback');
});

require __DIR__.'/auth.php';
