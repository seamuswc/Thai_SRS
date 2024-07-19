<?php

use Illuminate\Support\Facades\Route;



use App\Http\Controllers\FlashcardController;

Route::get('/', [FlashcardController::class, 'index']);
Route::post('/review', [FlashcardController::class, 'review']);
Route::get('/seed', [FlashcardController::class, 'seedFromJson']);
