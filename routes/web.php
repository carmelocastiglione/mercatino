<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProblemController;
use App\Http\Controllers\Student\ProblemController as StudentProblemController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Staff\ProblemController as StaffProblemController;
use App\Http\Controllers\Student\DeliveryController as StudentDeliveryController;
use App\Http\Controllers\Student\SalesController as StudentSalesController;
use App\Http\Controllers\Student\BookListingsController as StudentBookListingsController;
use App\Http\Controllers\Student\PurchasesController as StudentPurchasesController;
use App\Http\Controllers\Student\WithdrawalsController as StudentWithdrawalsController;
use App\Http\Controllers\Student\NotificationController as StudentNotificationController;
use App\Http\Controllers\Student\BookReservationController as StudentBookReservationController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\BookController as StaffBookController;
use App\Http\Controllers\Staff\DeliveryController as StaffDeliveryController;
use App\Http\Controllers\Staff\SchoolDeliveryDateController as StaffSchoolDeliveryDateController;
use App\Http\Controllers\Staff\SchoolWithdrawDateController as StaffSchoolWithdrawDateController;
use App\Http\Controllers\Staff\AcquisitionController as StaffAcquisitionController;
use App\Http\Controllers\Staff\SaleController as StaffSaleController;
use App\Http\Controllers\Staff\ReclaimController as StaffReclaimController;
use App\Http\Controllers\Staff\BookListingController as StaffBookListingController;
use App\Http\Controllers\Staff\WithdrawalController as StaffWithdrawalController;
use App\Http\Controllers\Staff\RegisterController as StaffRegisterController;
use App\Http\Controllers\Staff\UserHistoryController as StaffUserHistoryController;
use App\Http\Controllers\Staff\SettingsController as StaffSettingsController;
use App\Http\Controllers\Staff\BookReservationController as StaffBookReservationController;
use App\Http\Controllers\Staff\UserController as StaffUserController;
use App\Http\Controllers\Staff\ExportController as StaffExportController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SchoolController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\BookListingController;
use App\Http\Controllers\Auth\GoogleController;

/**
 * Health Check Route
 * Endpoint per monitoraggio e autoscaling su Laravel Cloud
 */
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toIso8601String(),
        'app' => config('app.name'),
    ]);
})->name('health');

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
    Route::get('/deliveries/status/submitted', [StudentDeliveryController::class, 'byStatus'])->defaults('status', 'submitted')->name('student.deliveries.submitted');
    Route::get('/deliveries/search-books', [StudentDeliveryController::class, 'searchBooks'])->name('student.deliveries.search-books');
    Route::get('/deliveries/delivery-dates', [StudentDeliveryController::class, 'getDeliveryDates'])->name('student.deliveries.delivery-dates');
    Route::post('/deliveries/add-to-cart', [StudentDeliveryController::class, 'addToCart'])->name('student.deliveries.add-to-cart');
    Route::get('/deliveries/create', [StudentDeliveryController::class, 'create'])->name('student.deliveries.create');
    Route::post('/deliveries', [StudentDeliveryController::class, 'store'])->name('student.deliveries.store');
    Route::post('/deliveries/batch', [StudentDeliveryController::class, 'storeMultiple'])->name('student.deliveries.batch.store');
    
    Route::get('/batches/{batch}/show', [StudentDeliveryController::class, 'showBatch'])->name('student.batches.show');
    Route::delete('/batches/{batch}', [StudentDeliveryController::class, 'destroyBatch'])->name('student.batches.delete');
    
    Route::get('/sales', [StudentSalesController::class, 'index'])->name('student.sales.index');
    
    Route::get('/book-listings', [StudentBookListingsController::class, 'index'])->name('student.book-listings.index');
    
    Route::get('/purchases', [StudentPurchasesController::class, 'index'])->name('student.purchases.index');
    
    Route::get('/withdrawals', [StudentWithdrawalsController::class, 'index'])->name('student.withdrawals.index');
    
    Route::get('/notifications', [StudentNotificationController::class, 'index'])->name('student.notifications.index');
    Route::get('/notifications/unread-count', [StudentNotificationController::class, 'getUnreadCount'])->name('student.notifications.unread-count');
    Route::patch('/notifications/{notification}/read', [StudentNotificationController::class, 'markAsRead'])->name('student.notifications.mark-as-read');
    Route::patch('/notifications/read-all', [StudentNotificationController::class, 'markAllAsRead'])->name('student.notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [StudentNotificationController::class, 'delete'])->name('student.notifications.delete');
    Route::post('/notifications/delete-all-read', [StudentNotificationController::class, 'deleteAllRead'])->name('student.notifications.delete-all-read');
    
    // Book Reservations Management - Prenotazioni libri
    Route::get('/book-reservations', [StudentBookReservationController::class, 'index'])->name('student.book-reservations.index');
    Route::get('/book-reservations/status/pending', [StudentBookReservationController::class, 'byStatus'])->defaults('status', 'pending')->name('student.book-reservations.pending');
    Route::get('/book-reservations/status/confirmed', [StudentBookReservationController::class, 'byStatus'])->defaults('status', 'confirmed')->name('student.book-reservations.confirmed');
    Route::get('/book-reservations/status/cancelled', [StudentBookReservationController::class, 'byStatus'])->defaults('status', 'cancelled')->name('student.book-reservations.cancelled');
    Route::get('/book-reservations/create', [StudentBookReservationController::class, 'create'])->name('student.book-reservations.create');
    Route::get('/book-reservations/search-acquisition-books', [StudentBookReservationController::class, 'searchAcquisitionBooks'])->name('student.book-reservations.search-acquisition-books');
    Route::post('/book-reservations/check-availability', [StudentBookReservationController::class, 'checkAvailability'])->name('student.book-reservations.check-availability');
    Route::post('/book-reservations', [StudentBookReservationController::class, 'store'])->name('student.book-reservations.store');
    Route::get('/book-reservations/{bookReservationBatch}', [StudentBookReservationController::class, 'show'])->name('student.book-reservations.show');
    Route::delete('/book-reservations/{bookReservationBatch}', [StudentBookReservationController::class, 'destroy'])->name('student.book-reservations.destroy');
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
    
    // Books Management - Catalog (RESTful)
    Route::get('/books', [StaffBookController::class, 'index'])->name('staff.books.index');
    Route::get('/books/create', [StaffBookController::class, 'create'])->name('staff.books.create');
    Route::post('/books', [StaffBookController::class, 'store'])->name('staff.books.store');
    Route::get('/books/{book}/edit', [StaffBookController::class, 'edit'])->name('staff.books.edit');
    Route::put('/books/{book}', [StaffBookController::class, 'update'])->name('staff.books.update');
    Route::delete('/books/{book}', [StaffBookController::class, 'destroy'])->name('staff.books.destroy');
    
    // Users Management (RESTful)
    Route::get('/users', [StaffUserController::class, 'index'])->name('staff.users.index');
    Route::get('/users/create', [StaffUserController::class, 'create'])->name('staff.users.create');
    Route::post('/users', [StaffUserController::class, 'store'])->name('staff.users.store');
    Route::get('/users/{user}/edit', [StaffUserController::class, 'edit'])->name('staff.users.edit');
    Route::put('/users/{user}', [StaffUserController::class, 'update'])->name('staff.users.update');
    
    // Deliveries Management
    Route::get('/deliveries', [StaffDeliveryController::class, 'index'])->name('staff.deliveries.index');
    Route::get('/deliveries/status/{status}', [StaffDeliveryController::class, 'byStatus'])
        ->where('status', 'pending|submitted')
        ->name('staff.deliveries.byStatus');
    Route::get('/deliveries/student/{studentId}', [StaffDeliveryController::class, 'studentDeliveries'])->name('staff.deliveries.student');
    Route::get('/deliveries/{delivery}', [StaffDeliveryController::class, 'show'])->name('staff.deliveries.show');
    Route::put('/deliveries/{delivery}/approve', [StaffDeliveryController::class, 'approve'])->name('staff.deliveries.approve');
    Route::get('/deliveries/{delivery}/reject', [StaffDeliveryController::class, 'rejectForm'])->name('staff.deliveries.reject-form');
    Route::put('/deliveries/{delivery}/reject', [StaffDeliveryController::class, 'reject'])->name('staff.deliveries.reject');
    Route::put('/deliveries/reject-single', [StaffDeliveryController::class, 'rejectDeliveryJson'])->name('staff.deliveries.reject-json');
    Route::put('/deliveries/approve-bulk', [StaffDeliveryController::class, 'approveBulk'])->name('staff.deliveries.approve-bulk');
    Route::put('/deliveries/update-batch-status', [StaffDeliveryController::class, 'updateBatchStatus'])->name('staff.deliveries.update-batch-status');
    
    // Settings Management - Impostazioni
    Route::get('/settings/general', [StaffSettingsController::class, 'general'])->name('staff.settings.general');
    Route::post('/settings/save', [StaffSettingsController::class, 'save'])->name('staff.settings.save');
    
    // Delivery Dates Management - Date di consegna
    Route::get('/delivery-dates', [StaffSchoolDeliveryDateController::class, 'index'])->name('staff.delivery-dates.index');
    Route::get('/delivery-dates/create', [StaffSchoolDeliveryDateController::class, 'create'])->name('staff.delivery-dates.create');
    Route::post('/delivery-dates', [StaffSchoolDeliveryDateController::class, 'store'])->name('staff.delivery-dates.store');
    Route::get('/delivery-dates/{deliveryDate}/edit', [StaffSchoolDeliveryDateController::class, 'edit'])->name('staff.delivery-dates.edit');
    Route::put('/delivery-dates/{deliveryDate}', [StaffSchoolDeliveryDateController::class, 'update'])->name('staff.delivery-dates.update');
    Route::delete('/delivery-dates/{deliveryDate}', [StaffSchoolDeliveryDateController::class, 'destroy'])->name('staff.delivery-dates.destroy');
    Route::patch('/delivery-dates/{deliveryDate}/toggle', [StaffSchoolDeliveryDateController::class, 'toggle'])->name('staff.delivery-dates.toggle');
    
    // Withdraw Dates Management - Date di ritiro
    Route::get('/withdraw-dates', [StaffSchoolWithdrawDateController::class, 'index'])->name('staff.withdraw-dates.index');
    Route::get('/withdraw-dates/create', [StaffSchoolWithdrawDateController::class, 'create'])->name('staff.withdraw-dates.create');
    Route::post('/withdraw-dates', [StaffSchoolWithdrawDateController::class, 'store'])->name('staff.withdraw-dates.store');
    Route::get('/withdraw-dates/{withdrawDate}/edit', [StaffSchoolWithdrawDateController::class, 'edit'])->name('staff.withdraw-dates.edit');
    Route::put('/withdraw-dates/{withdrawDate}', [StaffSchoolWithdrawDateController::class, 'update'])->name('staff.withdraw-dates.update');
    Route::delete('/withdraw-dates/{withdrawDate}', [StaffSchoolWithdrawDateController::class, 'destroy'])->name('staff.withdraw-dates.destroy');
    Route::patch('/withdraw-dates/{withdrawDate}/toggle', [StaffSchoolWithdrawDateController::class, 'toggle'])->name('staff.withdraw-dates.toggle');
    
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
    Route::delete('/acquisitions/{acquisition}', [StaffAcquisitionController::class, 'destroyAcquisition'])->name('staff.acquisitions.destroy');
    
    // Sales Management - Vendite al mercatino
    Route::get('/sales', [StaffSaleController::class, 'index'])->name('staff.sales.index');
    Route::get('/sales/create', [StaffSaleController::class, 'create'])->name('staff.sales.create');
    Route::get('/sales/{batch}/show', [StaffSaleController::class, 'show'])->name('staff.sales.show');
    Route::delete('/sales/{batch}', [StaffSaleController::class, 'destroyBatch'])->name('staff.sales.destroy');
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
    Route::put('/reclaims/{reclaim}/approve', [StaffReclaimController::class, 'approve'])->name('staff.reclaims.approve');
    Route::get('/reclaims/{reclaim}/reject-form', [StaffReclaimController::class, 'rejectForm'])->name('staff.reclaims.reject-form');
    Route::put('/reclaims/{reclaim}/reject', [StaffReclaimController::class, 'reject'])->name('staff.reclaims.reject');
    Route::delete('/reclaims/{reclaim}', [StaffReclaimController::class, 'destroy'])->name('staff.reclaims.destroy');
    
    // Withdrawals Management - Riscossioni
    Route::get('/withdrawals', [StaffWithdrawalController::class, 'index'])->name('staff.withdrawals.index');
    Route::get('/withdrawals/pending', [StaffWithdrawalController::class, 'pendingWithdrawals'])->name('staff.withdrawals.pending');
    Route::get('/withdrawals/create', [StaffWithdrawalController::class, 'create'])->name('staff.withdrawals.create');
    Route::get('/withdrawals/search-sellers', [StaffWithdrawalController::class, 'searchSellers'])->name('staff.withdrawals.search-sellers');
    Route::get('/withdrawals/{user}/process', [StaffWithdrawalController::class, 'processSeller'])->name('staff.withdrawals.process-seller');
    Route::post('/withdrawals/{listing}/withdraw-money', [StaffWithdrawalController::class, 'withdrawMoney'])->name('staff.withdrawals.withdraw-money');
    Route::post('/withdrawals/{listing}/withdraw-book', [StaffWithdrawalController::class, 'withdrawBook'])->name('staff.withdrawals.withdraw-book');
    Route::post('/withdrawals/{listing}/archive-book', [StaffWithdrawalController::class, 'archiveBook'])->name('staff.withdrawals.archive-book');
    Route::post('/withdrawals/{user}/withdraw-all-books', [StaffWithdrawalController::class, 'withdrawAllBooks'])->name('staff.withdrawals.withdraw-all-books');
    Route::post('/withdrawals/{user}/withdraw-all-sold-books', [StaffWithdrawalController::class, 'withdrawAllSoldBooks'])->name('staff.withdrawals.withdraw-all-sold-books');
    Route::post('/withdrawals/{user}/process-complete', [StaffWithdrawalController::class, 'processComplete'])->name('staff.withdrawals.process-complete');
    Route::get('/withdrawal-batches/{withdrawalBatch}', [StaffWithdrawalController::class, 'showBatch'])->name('staff.withdrawals.show-batch');
    Route::delete('/withdrawal-batches/{withdrawalBatch}', [StaffWithdrawalController::class, 'destroyWithdrawalBatch'])->name('staff.withdrawals.destroy-batch');
    Route::get('/pickup-batches/{pickupBatch}', [StaffWithdrawalController::class, 'showPickupBatch'])->name('staff.withdrawals.pickup-summary');
    Route::delete('/pickup-batches/{pickupBatch}', [StaffWithdrawalController::class, 'destroyPickupBatch'])->name('staff.withdrawals.destroy-pickup-batch');
    Route::get('/withdrawals/{withdrawal}/show', [StaffWithdrawalController::class, 'show'])->name('staff.withdrawals.show');
    Route::post('/withdrawals', [StaffWithdrawalController::class, 'store'])->name('staff.withdrawals.store');
    
    // User Registration (for staff to register new sellers quickly)
    Route::post('/register-user', [StaffRegisterController::class, 'registerUser'])->name('staff.register-user');
    
    // User History - Storico utente
    Route::get('/storico', [StaffUserHistoryController::class, 'index'])->name('staff.user-history.index');
    Route::get('/storico/search', [StaffUserHistoryController::class, 'search'])->name('staff.user-history.search');
    Route::get('/storico/{user}', [StaffUserHistoryController::class, 'show'])->name('staff.user-history.show');
    
    // Export - Esportazione dati
    Route::get('/esporta', [StaffExportController::class, 'index'])->name('staff.export.index');
    Route::get('/esporta/download/{type}', [StaffExportController::class, 'download'])->name('staff.export.download');
    
    // Book Reservations Management - Prenotazioni libri
    Route::get('/book-reservations', [StaffBookReservationController::class, 'index'])->name('staff.book-reservations.index');
    Route::get('/book-reservations/status/{status}', [StaffBookReservationController::class, 'byStatus'])
        ->where('status', 'pending|confirmed|cancelled')
        ->name('staff.book-reservations.byStatus');
    Route::get('/book-reservations/search-students', [StaffBookReservationController::class, 'searchStudents'])->name('staff.book-reservations.search-students');
    Route::get('/book-reservations/student/{studentId}', [StaffBookReservationController::class, 'studentReservations'])->name('staff.book-reservations.student');
    Route::get('/book-reservations/prepare-sales', [StaffBookReservationController::class, 'prepareSales'])->name('staff.book-reservations.prepare-sales');
    Route::post('/book-reservations/store-session-approvals', [StaffBookReservationController::class, 'storeSessionApprovals'])->name('staff.book-reservations.store-session-approvals');
    Route::get('/book-reservations/{bookReservationBatch}', [StaffBookReservationController::class, 'show'])->name('staff.book-reservations.show');
    Route::post('/book-reservations/{bookReservationBatch}/confirm', [StaffBookReservationController::class, 'confirm'])->name('staff.book-reservations.confirm');
    Route::post('/book-reservations/{bookReservationBatch}/reject', [StaffBookReservationController::class, 'reject'])->name('staff.book-reservations.reject');
    Route::put('/book-reservations/approve-single', [StaffBookReservationController::class, 'approveSingle'])->name('staff.book-reservations.approve-single');
    Route::put('/book-reservations/reject-single', [StaffBookReservationController::class, 'rejectSingle'])->name('staff.book-reservations.reject-single');
    Route::put('/book-reservations/approve-bulk', [StaffBookReservationController::class, 'approveBulk'])->name('staff.book-reservations.approve-bulk');
    Route::put('/book-reservations/reject-multiple', [StaffBookReservationController::class, 'rejectMultiple'])->name('staff.book-reservations.reject-multiple');
    Route::put('/book-reservations/update-batch-status', [StaffBookReservationController::class, 'updateBatchStatus'])->name('staff.book-reservations.update-batch-status');
    Route::post('/book-reservations/create-sales', [StaffBookReservationController::class, 'createSalesBulk'])->name('staff.book-reservations.create-sales');
});

/**
 * Problem Reporting Routes
 * Segnalazione problemi per studenti, staff e gestione per admin
 */
Route::middleware(['auth', 'student'])->prefix('student')->group(function () {
    Route::get('/segnala-problema', [StudentProblemController::class, 'create'])->name('student.problems.create');
    Route::post('/segnala-problema', [StudentProblemController::class, 'store'])->name('student.problems.store');
});

Route::middleware(['auth', 'staff'])->prefix('staff')->group(function () {
    Route::get('/segnala-problema', [StaffProblemController::class, 'create'])->name('staff.problems.create');
    Route::post('/segnala-problema', [StaffProblemController::class, 'store'])->name('staff.problems.store');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/problemi', [ProblemController::class, 'adminIndex'])->name('admin.problems.index');
    Route::put('/admin/problemi/{problem}/corretto', [ProblemController::class, 'resolve'])->name('admin.problems.resolve');
    Route::put('/admin/problemi/{problem}/elimina', [ProblemController::class, 'delete'])->name('admin.problems.delete');
});
