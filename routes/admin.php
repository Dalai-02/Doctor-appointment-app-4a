<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
   return view('admin.dashboard');
})->name('dashboard');

//Gestion de ROles
Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);

//Gestión de usuarios
Route::resource('users', \App\Http\Controllers\Admin\UserController::class);

//Gestion de Pacientes
Route::resource('patients', \App\Http\Controllers\Admin\PatientController::class);

//Gestion de Doctores
Route::resource('doctors', \App\Http\Controllers\Admin\DoctorController::class);

//Gestión de Aseguradoras
Route::resource('insurances', \App\Http\Controllers\Admin\InsuranceController::class);

//Gestión de Sugerencias/Feedback
Route::resource('feedbacks', \App\Http\Controllers\Admin\FeedbackController::class);



