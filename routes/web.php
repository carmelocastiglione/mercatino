<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;

/**
 * Home Route
 * Visualizza la home page del mercatino di libri usati
 */
Route::get('/', [HomeController::class, 'index'])->name('home');

/**
 * Authentication Routes
 * Gestione del login e logout degli utenti
 */
Route::get('/login', [LoginController::class, 'show'])->name('login.show');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
