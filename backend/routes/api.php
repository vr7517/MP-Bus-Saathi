<?php

use App\Http\Controllers\Api\BusStopController;
use App\Http\Controllers\Api\CommunityVerificationController;
use App\Http\Controllers\Api\RouteController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\SyncController;
use App\Http\Controllers\Api\TelemetryController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Bus Stops
    Route::get('/stops', [BusStopController::class, 'index']);
    Route::get('/stops/{id}', [BusStopController::class, 'show']);

    // Routes & Route Finder
    Route::get('/routes', [RouteController::class, 'index']);
    Route::get('/routes/search', [RouteController::class, 'search']);
    Route::get('/routes/{id}/stops', [RouteController::class, 'stops']);

    // Timetables & Schedules
    Route::get('/schedules/route/{routeId}', [ScheduleController::class, 'byRoute']);

    // GPS Telemetry & Live Tracking
    Route::get('/buses/{id}/live-status', [TelemetryController::class, 'status']);
    Route::post('/telemetry/ping', [TelemetryController::class, 'ping']);

    // Community Verification
    Route::post('/community/verify', [CommunityVerificationController::class, 'store']);
    Route::get('/community/schedule/{scheduleId}', [CommunityVerificationController::class, 'index']);

    // Offline Sync Snapshot for Rural Devices
    Route::get('/sync/snapshot', [SyncController::class, 'snapshot']);
});

