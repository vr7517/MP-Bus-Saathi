<?php

use App\Livewire\Admin\BusManager;
use App\Livewire\Admin\DashboardOverview;
use App\Livewire\Admin\LiveTracker;
use App\Livewire\Admin\RouteManager;
use App\Livewire\Admin\ScheduleManager;
use App\Livewire\Admin\VerificationModerator;
use App\Livewire\Auth\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

// Authentication
Route::get('/login', Login::class)->name('login')->middleware('guest');
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout')->middleware('auth');

// Protected Admin & Operator Portal
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', DashboardOverview::class)->name('admin.dashboard');
    Route::get('/buses', BusManager::class)->name('admin.buses');
    Route::get('/routes', RouteManager::class)->name('admin.routes');
    Route::get('/schedules', ScheduleManager::class)->name('admin.schedules');
    Route::get('/tracking', LiveTracker::class)->name('admin.tracking');
    Route::get('/verifications', VerificationModerator::class)->name('admin.verifications');
});
