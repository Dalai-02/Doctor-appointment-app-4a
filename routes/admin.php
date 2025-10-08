<?php

use Illuminate\Support\Facades\Route;

<<<<<<< HEAD
Route::get('/', function () {
   return view('admin.dashboard');
})->name('dashboard');
=======
Route::get('/', function(){
    return view('admin.dashboard');
})-> name('admin.dashboard');
>>>>>>> 249b43ae89a259d1552be25f196090e08bacb3b8
