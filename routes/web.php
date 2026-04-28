<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

/**
 * Home Route
 * Visualizza la home page del mercatino di libri usati
 */
Route::get('/', [HomeController::class, 'index'])->name('home');
