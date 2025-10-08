<?php

use Illuminate\Support\Facades\Route;

<<<<<<< HEAD
Route::redirect('/', 'admin');

//
//Route::get('/', function () {
//    return view('welcome');
//});
=======
Route::redirect('/','admin');
// Route::get('/', function () {
//    return view('welcome');
// });
>>>>>>> 249b43ae89a259d1552be25f196090e08bacb3b8

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
