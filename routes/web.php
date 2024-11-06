<?php

use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TagController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

//All routes that are guarded by the ban middleware
Route::middleware(['banned'])->group(function () {
    // Home/Dashboard
    Route::view('/', 'dashboard')
        ->name('dashboard');
    Route::get('/home', function () {
        return redirect()->route('dashboard');
    })->name('home');

    //User
    Route::view('profile', 'profile')
        ->middleware(['auth'])
        ->name('profile');
    Route::get('/user/{id}', [UserController::class, 'show']);
    Route::get('/user-followers/{id}', [UserController::class, 'showFollowers']);
    Route::get('/user-followings/{id}', [UserController::class, 'showFollowings']);

    //Posts
    Route::get('/post/{id}', [PostController::class, 'show']);
    Route::view('drafts', 'drafts')
        ->middleware(['auth'])
        ->name('drafts');
    Route::view('queue', 'queued-posts')
        ->middleware(['auth'])
        ->name('queue');

    //Tags
    Route::get('/tag/{id}', [TagController::class, 'show']);

    //Search
    Route::get('/search', [SearchController::class, 'search'])
        ->name('search');

    //Messages
    Route::get('/messages', [MessageController::class, 'show'])
        ->middleware(['auth']);
    Route::get('/messages/user/{targetId}', [MessageController::class, 'showUserConversation'])
        ->middleware(['auth']);
    Route::get('/messages/group', [MessageController::class, 'showGroupConversation'])
        ->middleware(['auth']);
    Route::get('/messages/group/{groupId}', [MessageController::class, 'showGroupConversation'])
        ->middleware(['auth']);

    // Route::get('/messages', [MessageController::class, 'show'])
    //     ->middleware(['auth']);
    // Route::get('/messages/{targetId}', [MessageController::class, 'show'])
    //     ->middleware(['auth']);

    //Notifications
    Route::view('/notifications', 'notifications')
        ->middleware(['auth'])
        ->name('notifications');

    //AdminPost
    Route::view('adminPage', 'adminPage')
        ->middleware(['auth', 'admin'])
        ->name('adminPage');

    //Settings
    Route::view('settings', 'settings')
    ->middleware(['auth'])
    ->name('settings');

    //AdminMessage
    Route::view('adminPageMessage', 'adminPageMessage')
    ->middleware(['auth', 'admin'])
    ->name('adminPageMessage');

    // blockedUserList
    Route::view('blockedUsers', 'blockedUsers')
        ->middleware(['auth'])
        ->name('blockedUsers');
});


//The page that banned users see
Route::view('banned', 'banned')
    ->name('banned');
    

//Emails
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