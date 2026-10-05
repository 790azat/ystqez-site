<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\VideoController;
use App\Livewire;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/lang/{locale}', LocaleController::class)->whereIn('locale', array_keys(\App\Support\Locale::SUPPORTED))->name('locale');

Route::get('/videos', Livewire\VideoCatalog::class)->name('videos.index');
Route::get('/videos/{slug}', [VideoController::class, 'show'])->name('videos.show');

Route::get('/chat', Livewire\ChatRoom::class)->name('chat');
Route::get('/collab', Livewire\CollabForm::class)->name('collab');
Route::get('/instagram', [PageController::class, 'instagram'])->name('instagram');

Route::get('/forum', Livewire\Forum\Index::class)->name('forum.index');
Route::get('/forum/c/{category:slug}', Livewire\Forum\CategoryShow::class)->name('forum.category');
Route::get('/forum/t/{topic}', Livewire\Forum\TopicShow::class)->name('forum.topic');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store')->middleware('throttle:10,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store')->middleware('throttle:5,1');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    Route::get('/', Livewire\Admin\Dashboard::class)->name('dashboard');
    Route::get('/videos', Livewire\Admin\Videos::class)->name('videos');
    Route::get('/collabs', Livewire\Admin\Collabs::class)->name('collabs');
    Route::get('/forum', Livewire\Admin\Forum::class)->name('forum');
    Route::get('/chat', Livewire\Admin\Chat::class)->name('chat');
    Route::get('/comments', Livewire\Admin\Comments::class)->name('comments');
    Route::get('/settings', Livewire\Admin\Settings::class)->name('settings');
    Route::get('/users', Livewire\Admin\Users::class)->name('users');
});

Route::get('/_setup/{token}', SetupController::class)->name('setup');
