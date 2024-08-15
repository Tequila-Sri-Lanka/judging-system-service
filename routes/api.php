<?php

use App\Http\Controllers\AdminControllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//admin api
Route::post('/login', [AdminControllers::class, 'login'])->name('login');
Route::post('/register', [AdminControllers::class, 'register'])->name('register');
Route::get('/logout', [AdminControllers::class, 'logout'])->name('logout');
Route::get('getAllUser', [AdminControllers::class, 'getAllUser']);
Route::get('/userDetails', [AdminControllers::class, 'userDetails'])->name('userDetails');
Route::put('/userUpdate/{id}', [AdminControllers::class, 'userUpdate'])->name('userUpdate');
