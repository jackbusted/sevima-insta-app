<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\HomeUserController;
use App\Http\Controllers\HomeUserHistoryController;

use App\Http\Controllers\ManageQuestionController;
use App\Http\Controllers\HomeAdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\QuizController;

Route::get('/', function () {
    if(Auth::check()) {
        $user = Auth::user();

        if ($user->role_id == 1) {
            return redirect('/admin-manage');
        } elseif ($user->role_id == 3) {
            return redirect('/homeuser');
        } else {
            // role_id lain

            return redirect('/unauthorized-account'); // nanti buatkan halaman sendiri
        }
    } else {
        return redirect('/login');
    }
});

Route::get('/dbconn', function () {
    return view('dbconnect', [
        "title" => "Testing Database Connection"
    ]);
});

Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout']);

Route::get('/register', [RegisterController::class, 'index']);
Route::post('/register', [RegisterController::class, 'store'])->name('register.save');

Route::middleware(['auth', 'admin'])->group(function () {
    // first page after login (admin)
    Route::get('/admin-manage', [HomeAdminController::class, 'index'])->name('home-admin.dashboard');

    // setting profile
    Route::get('/admin-manage/setting-profile', [HomeAdminController::class, 'settingForm'])->name('admin.setting-profile.form');
    Route::post('/admin-manage/setting-profile/avatar-preview', [HomeAdminController::class, 'avatarImagePreview'])->name('admin.setting-profile.avatar-preview');
    Route::post('/admin-manage/setting-profile/update', [HomeAdminController::class, 'updateSettingProfile'])->name('admin.setting-profile.update');
    Route::post('/admin-manage/setting-profile/delete-avatar', [HomeAdminController::class, 'deleteAvatar'])->name('admin.setting-profile.delete-avatar');
});

Route::middleware(['auth', 'user'])->group(function () {
    // dashboard / halaman utama user
    Route::get('/homeuser', [HomeUserController::class, 'index'])->name('tampilkan-halaman-utama');
    Route::post('/homeuser/load-feed', [HomeUserController::class, 'loadFeed'])->name('homeuser.posts.load-feed');

    // post new feed
    Route::post('/homeuser/image-preview', [HomeUserController::class, 'imagePreview'])->name('homeuser.posts.image-preview');
    Route::post('/homeuser/post-feed', [HomeUserController::class, 'postFeed'])->name('homeuser.posts.new-feed');

    // like and comments
    Route::post('/homeuser/{id}/like', [HomeUserController::class, 'toggleLike'])->name('homeuser.posts.like');
    Route::post('/homeuser/{id}/comment', [HomeUserController::class, 'addComment'])->name('homeuser.posts.comment');

    // setting user
    Route::get('/homeuser/setting-user', [RegisterController::class, 'settingUserForm'])->name('homeuser.setting-user');
    Route::post('/homeuser/setting-user/avatar-preview', [RegisterController::class, 'avatarImagePreview'])->name('homeuser.setting-user.avatar-preview');
    Route::post('/homeuser/setting-user/update', [RegisterController::class, 'updateUserProfile'])->name('homeuser.setting-user.update');
    Route::post('/homeuser/setting-user/delete-avatar', [RegisterController::class, 'deleteAvatar'])->name('homeuser.setting-user.delete-avatar');
});