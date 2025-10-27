<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
   return view('admin.dashboard');
})->name('dashboard');

//Gestion de ROles
Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);