<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminController;

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

/**
 * Admin Routes
 * Dashboard e gestione della piattaforma (solo per admin)
 */
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Schools Management
    Route::get('/schools', [AdminController::class, 'schools'])->name('admin.schools');
    Route::get('/schools/create', [AdminController::class, 'createSchool'])->name('admin.schools.create');
    Route::post('/schools', [AdminController::class, 'storeSchool'])->name('admin.schools.store');
    Route::get('/schools/{school}/edit', [AdminController::class, 'editSchool'])->name('admin.schools.edit');
    Route::put('/schools/{school}', [AdminController::class, 'updateSchool'])->name('admin.schools.update');
    Route::delete('/schools/{school}', [AdminController::class, 'deleteSchool'])->name('admin.schools.delete');
    
    // Users Management
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    
    // Books Management
    Route::get('/books', [AdminController::class, 'books'])->name('admin.books');
    Route::get('/books/create', [AdminController::class, 'createBook'])->name('admin.books.create');
    Route::post('/books', [AdminController::class, 'storeBook'])->name('admin.books.store');
    Route::get('/books/{book}/edit', [AdminController::class, 'editBook'])->name('admin.books.edit');
    Route::put('/books/{book}', [AdminController::class, 'updateBook'])->name('admin.books.update');
    Route::delete('/books/{book}', [AdminController::class, 'deleteBook'])->name('admin.books.delete');
});
