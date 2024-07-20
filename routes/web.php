<?php

use Illuminate\Support\Facades\Route;



use App\Http\Controllers\FlashcardController;
use App\Http\Controllers\AuthController;


Route::get('/', [FlashcardController::class, 'index']);
Route::post('/review', [FlashcardController::class, 'review']);
Route::get('/seed', [FlashcardController::class, 'seedFromJson']);


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

