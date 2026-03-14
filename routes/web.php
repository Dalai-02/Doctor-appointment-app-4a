<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\DoctorScheduleController;

Route::redirect('/', 'admin');

//
//Route::get('/', function () {
//    return view('welcome');
//});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('appointments', AppointmentController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);
        Route::get('consultations/{appointment}', [AppointmentController::class, 'consultation'])->name('consultations.show');
        Route::get('doctors/{doctor}/schedules', [DoctorScheduleController::class, 'edit'])->name('doctors.schedules.edit');
        Route::put('doctors/{doctor}/schedules', [DoctorScheduleController::class, 'update'])->name('doctors.schedules.update');
        Route::view('calendars', 'admin.calendars.index')->name('calendars.index');
        Route::view('support', 'admin.support.index')->name('support.index');
    });
