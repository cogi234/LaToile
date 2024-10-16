<?php

use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TagController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;


Route::view('/', 'dashboard')
    ->name('dashboard');

Route::get('/search', [SearchController::class, 'search'])->name('search');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::view('adminPage', 'adminPage')
    ->middleware(['auth'])
    ->name('adminPage');

Route::view('drafts', 'drafts')
    ->middleware(['auth'])
    ->name('drafts');

Route::view('queue', 'queued-posts')
    ->middleware(['auth'])
    ->name('queue');

Route::get('/post/{id}', [PostController::class, 'show']);
Route::get('/user/{id}', [UserController::class, 'show']);
Route::get('/tag/{id}', [TagController::class, 'show']);
Route::get('/messages', [MessageController::class, 'show']);
Route::get('/messages/{currentId}-{targetId}', [MessageController::class, 'show']);


Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

require __DIR__ . '/auth.php';
