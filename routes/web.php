<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\DeliveryController as StudentDeliveryController;
use App\Http\Controllers\Student\SalesController as StudentSalesController;
use App\Http\Controllers\Student\PurchasesController as StudentPurchasesController;
use App\Http\Controllers\Student\WithdrawalsController as StudentWithdrawalsController;
use App\Http\Controllers\Student\NotificationController as StudentNotificationController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\DeliveryController as StaffDeliveryController;
use App\Http\Controllers\Staff\SchoolDeliveryDateController as StaffSchoolDeliveryDateController;
use App\Http\Controllers\Staff\AcquisitionController as StaffAcquisitionController;
use App\Http\Controllers\Staff\SaleController as StaffSaleController;
use App\Http\Controllers\Staff\ReclaimController as StaffReclaimController;
use App\Http\Controllers\Staff\BookListingController as StaffBookListingController;
use App\Http\Controllers\Staff\WithdrawalController as StaffWithdrawalController;
use App\Http\Controllers\Staff\RegisterController as StaffRegisterController;
use App\Http\Controllers\Staff\UserHistoryController as StaffUserHistoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SchoolController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\BookListingController;
use App\Http\Controllers\Auth\GoogleController;

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
 * Google SSO Routes
 * Autenticazione tramite Google OAuth
 */
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

/**
 * Student Routes
 * Dashboard e gestione consegne per gli studenti
 */
Route::middleware(['auth', 'student'])->prefix('student')->group(function () {
    Route::get('/', [StudentDashboardController::class, 'index'])->name('student.dashboard');
    
    Route::get('/deliveries', [StudentDeliveryController::class, 'index'])->name('student.deliveries.index');
    Route::get('/deliveries/status/pending', [StudentDeliveryController::class, 'byStatus'])->defaults('status', 'pending')->name('student.deliveries.pending');
    Route::get('/deliveries/status/approved', [StudentDeliveryController::class, 'byStatus'])->defaults('status', 'approved')->name('student.deliveries.approved');
    Route::get('/deliveries/status/rejected', [StudentDeliveryController::class, 'byStatus'])->defaults('status', 'rejected')->name('student.deliveries.rejected');
    Route::get('/deliveries/search-books', [StudentDeliveryController::class, 'searchBooks'])->name('student.deliveries.search-books');
    Route::get('/deliveries/delivery-dates', [StudentDeliveryController::class, 'getDeliveryDates'])->name('student.deliveries.delivery-dates');
    Route::post('/deliveries/add-to-cart', [StudentDeliveryController::class, 'addToCart'])->name('student.deliveries.add-to-cart');
    Route::get('/deliveries/create', [StudentDeliveryController::class, 'create'])->name('student.deliveries.create');
    Route::post('/deliveries', [StudentDeliveryController::class, 'store'])->name('student.deliveries.store');
    Route::post('/deliveries/batch', [StudentDeliveryController::class, 'storeMultiple'])->name('student.deliveries.batch.store');
    Route::get('/deliveries/{delivery}/edit', [StudentDeliveryController::class, 'edit'])->name('student.deliveries.edit');
    Route::put('/deliveries/{delivery}', [StudentDeliveryController::class, 'update'])->name('student.deliveries.update');
    Route::delete('/deliveries/{delivery}', [StudentDeliveryController::class, 'destroy'])->name('student.deliveries.delete');
    
    Route::get('/batches/{batch}/show', [StudentDeliveryController::class, 'showBatch'])->name('student.batches.show');
    
    Route::get('/sales', [StudentSalesController::class, 'index'])->name('student.sales.index');
    Route::get('/sales/{sale}', [StudentSalesController::class, 'show'])->name('student.sales.show');
    
    Route::get('/purchases', [StudentPurchasesController::class, 'index'])->name('student.purchases.index');
    Route::get('/purchases/{purchase}', [StudentPurchasesController::class, 'show'])->name('student.purchases.show');
    
    Route::get('/withdrawals', [StudentWithdrawalsController::class, 'index'])->name('student.withdrawals.index');
    Route::get('/withdrawals/{withdrawal}', [StudentWithdrawalsController::class, 'show'])->name('student.withdrawals.show');
    
    Route::get('/notifications', [StudentNotificationController::class, 'index'])->name('student.notifications.index');
    Route::get('/notifications/unread-count', [StudentNotificationController::class, 'getUnreadCount'])->name('student.notifications.unread-count');
    Route::patch('/notifications/{notification}/read', [StudentNotificationController::class, 'markAsRead'])->name('student.notifications.mark-as-read');
    Route::patch('/notifications/read-all', [StudentNotificationController::class, 'markAllAsRead'])->name('student.notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [StudentNotificationController::class, 'delete'])->name('student.notifications.delete');
    Route::post('/notifications/delete-all-read', [StudentNotificationController::class, 'deleteAllRead'])->name('student.notifications.delete-all-read');
});

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

/**
 * Staff Routes
 * Dashboard e gestione approvazione consegne (per staff e admin)
 */
Route::middleware(['auth', 'staff'])->prefix('staff')->group(function () {
    Route::get('/', [StaffDashboardController::class, 'index'])->name('staff.dashboard');
    
    // Deliveries Management
    Route::get('/deliveries', [StaffDeliveryController::class, 'index'])->name('staff.deliveries.index');
    Route::get('/deliveries/student/{studentId}', [StaffDeliveryController::class, 'studentDeliveries'])->name('staff.deliveries.student');
    Route::get('/deliveries/{delivery}', [StaffDeliveryController::class, 'show'])->name('staff.deliveries.show');
    Route::put('/deliveries/{delivery}/approve', [StaffDeliveryController::class, 'approve'])->name('staff.deliveries.approve');
    Route::get('/deliveries/{delivery}/reject', [StaffDeliveryController::class, 'rejectForm'])->name('staff.deliveries.reject-form');
    Route::put('/deliveries/{delivery}/reject', [StaffDeliveryController::class, 'reject'])->name('staff.deliveries.reject');
    Route::put('/deliveries/reject-single', [StaffDeliveryController::class, 'rejectDeliveryJson'])->name('staff.deliveries.reject-json');
    Route::put('/deliveries/approve-bulk', [StaffDeliveryController::class, 'approveBulk'])->name('staff.deliveries.approve-bulk');
    
    // Delivery Dates Management - Date di consegna
    Route::get('/delivery-dates', [StaffSchoolDeliveryDateController::class, 'index'])->name('staff.delivery-dates.index');
    Route::get('/delivery-dates/create', [StaffSchoolDeliveryDateController::class, 'create'])->name('staff.delivery-dates.create');
    Route::post('/delivery-dates', [StaffSchoolDeliveryDateController::class, 'store'])->name('staff.delivery-dates.store');
    Route::get('/delivery-dates/{deliveryDate}/edit', [StaffSchoolDeliveryDateController::class, 'edit'])->name('staff.delivery-dates.edit');
    Route::put('/delivery-dates/{deliveryDate}', [StaffSchoolDeliveryDateController::class, 'update'])->name('staff.delivery-dates.update');
    Route::delete('/delivery-dates/{deliveryDate}', [StaffSchoolDeliveryDateController::class, 'destroy'])->name('staff.delivery-dates.destroy');
    Route::patch('/delivery-dates/{deliveryDate}/toggle', [StaffSchoolDeliveryDateController::class, 'toggle'])->name('staff.delivery-dates.toggle');
    
    // Book Listings - Libri disponibili
    Route::get('/book-listings', [StaffBookListingController::class, 'index'])->name('staff.book-listings.index');
    
    // Acquisitions Management - Acquisizioni
    Route::get('/acquisitions', [StaffAcquisitionController::class, 'index'])->name('staff.acquisitions.index');
    Route::get('/acquisitions/create', [StaffAcquisitionController::class, 'create'])->name('staff.acquisitions.create');
    Route::get('/acquisitions/{acquisition}/show', [StaffAcquisitionController::class, 'show'])->name('staff.acquisitions.show');
    Route::get('/acquisitions/search-books', [StaffAcquisitionController::class, 'searchBooks'])->name('staff.acquisitions.search-books');
    Route::get('/acquisitions/search-sellers', [StaffAcquisitionController::class, 'searchSellers'])->name('staff.acquisitions.search-sellers');
    Route::get('/acquisitions/search-students', [StaffAcquisitionController::class, 'searchStudents'])->name('staff.acquisitions.search-students');
    Route::get('/acquisitions/student-deliveries', [StaffAcquisitionController::class, 'getStudentDeliveries'])->name('staff.acquisitions.student-deliveries');
    Route::post('/acquisitions/batch', [StaffAcquisitionController::class, 'storeBatch'])->name('staff.acquisitions.store-batch');
    Route::put('/acquisitions/{listing}/mark-sold', [StaffAcquisitionController::class, 'markAsSold'])->name('staff.acquisitions.mark-sold');
    Route::post('/acquisitions/create-book', [StaffAcquisitionController::class, 'createBook'])->name('staff.acquisitions.create-book');
    
    // Sales Management - Vendite al mercatino
    Route::get('/sales', [StaffSaleController::class, 'index'])->name('staff.sales.index');
    Route::get('/sales/create', [StaffSaleController::class, 'create'])->name('staff.sales.create');
    Route::get('/sales/batch-summary', [StaffSaleController::class, 'batchSummary'])->name('staff.sales.batch-summary');
    Route::get('/sales/{sale}/show', [StaffSaleController::class, 'show'])->name('staff.sales.show');
    Route::get('/sales/search-buyers', [StaffSaleController::class, 'searchBuyers'])->name('staff.sales.search-buyers');
    Route::get('/sales/search-listings', [StaffSaleController::class, 'searchListings'])->name('staff.sales.search-listings');
    Route::post('/sales/batch', [StaffSaleController::class, 'storeBatch'])->name('staff.sales.store-batch');
    Route::post('/sales', [StaffSaleController::class, 'store'])->name('staff.sales.store');
    
    // Reclaims Management - Resi
    Route::get('/reclaims', [StaffReclaimController::class, 'index'])->name('staff.reclaims.index');
    Route::get('/reclaims/search-buyers', [StaffReclaimController::class, 'searchBuyers'])->name('staff.reclaims.search-buyers');
    Route::get('/reclaims/buyer-books', [StaffReclaimController::class, 'getBuyerBooks'])->name('staff.reclaims.buyer-books');
    Route::get('/reclaims/create', [StaffReclaimController::class, 'create'])->name('staff.reclaims.create');
    Route::post('/reclaims', [StaffReclaimController::class, 'store'])->name('staff.reclaims.store');
    Route::get('/reclaims/{reclaim}', [StaffReclaimController::class, 'show'])->name('staff.reclaims.show');
    Route::delete('/reclaims/{reclaim}', [StaffReclaimController::class, 'destroy'])->name('staff.reclaims.destroy');
    
    // Withdrawals Management - Riscossioni
    Route::get('/withdrawals', [StaffWithdrawalController::class, 'index'])->name('staff.withdrawals.index');
    Route::get('/withdrawals/create', [StaffWithdrawalController::class, 'create'])->name('staff.withdrawals.create');
    Route::get('/withdrawals/search-sellers', [StaffWithdrawalController::class, 'searchSellers'])->name('staff.withdrawals.search-sellers');
    Route::get('/withdrawals/{user}/process', [StaffWithdrawalController::class, 'processSeller'])->name('staff.withdrawals.process-seller');
    Route::post('/withdrawals/{listing}/withdraw-money', [StaffWithdrawalController::class, 'withdrawMoney'])->name('staff.withdrawals.withdraw-money');
    Route::post('/withdrawals/{listing}/withdraw-book', [StaffWithdrawalController::class, 'withdrawBook'])->name('staff.withdrawals.withdraw-book');
    Route::get('/withdrawals/{withdrawal}/show', [StaffWithdrawalController::class, 'show'])->name('staff.withdrawals.show');
    Route::post('/withdrawals', [StaffWithdrawalController::class, 'store'])->name('staff.withdrawals.store');
    
    // User Registration (for staff to register new sellers quickly)
    Route::post('/register-user', [StaffRegisterController::class, 'registerUser'])->name('staff.register-user');
    
    // User History - Storico utente
    Route::get('/storico', [StaffUserHistoryController::class, 'index'])->name('staff.user-history.index');
    Route::get('/storico/search', [StaffUserHistoryController::class, 'search'])->name('staff.user-history.search');
    Route::get('/storico/user/{user}', [StaffUserHistoryController::class, 'show'])->name('staff.user-history.show');
});
