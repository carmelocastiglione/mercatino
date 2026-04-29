<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SchoolController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\BookListingController;

/**
 * Home Route
 * Visualizza la home page del mercatino di libri usati
 */
Route::get('/', [HomeController::class, 'index'])->name('home');

/**
 * Authentication Routes
 * Gestione del login e logout degli utenti
 */
// Alias route for Laravel's default redirect when session expires
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/**
 * Admin Routes
 * Dashboard e gestione della piattaforma (solo per admin)
 */
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Schools Management (RESTful)
    Route::get('/schools', [SchoolController::class, 'index'])->name('admin.schools.index');
    Route::get('/schools/create', [SchoolController::class, 'create'])->name('admin.schools.create');
    Route::post('/schools', [SchoolController::class, 'store'])->name('admin.schools.store');
    Route::get('/schools/{school}/edit', [SchoolController::class, 'edit'])->name('admin.schools.edit');
    Route::put('/schools/{school}', [SchoolController::class, 'update'])->name('admin.schools.update');
    Route::delete('/schools/{school}', [SchoolController::class, 'destroy'])->name('admin.schools.delete');
    
    // Users Management (RESTful)
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.delete');
    
    // Books Management - Catalog (RESTful)
    Route::get('/books', [BookController::class, 'index'])->name('admin.books.index');
    Route::get('/books/create', [BookController::class, 'create'])->name('admin.books.create');
    Route::post('/books', [BookController::class, 'store'])->name('admin.books.store');
    Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('admin.books.edit');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('admin.books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('admin.books.delete');
    
    // Book Listings Management - Copies for sale (RESTful)
    Route::get('/listings', [BookListingController::class, 'index'])->name('admin.listings.index');
    Route::get('/listings/create', [BookListingController::class, 'create'])->name('admin.listings.create');
    Route::post('/listings', [BookListingController::class, 'store'])->name('admin.listings.store');
    Route::get('/listings/{listing}/edit', [BookListingController::class, 'edit'])->name('admin.listings.edit');
    Route::put('/listings/{listing}', [BookListingController::class, 'update'])->name('admin.listings.update');
    Route::delete('/listings/{listing}', [BookListingController::class, 'destroy'])->name('admin.listings.delete');
});
