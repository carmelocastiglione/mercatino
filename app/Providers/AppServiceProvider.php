<?php

namespace App\Providers;

use App\Models\BookReservationBatch;
use App\Policies\BookReservationBatchPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(BookReservationBatch::class, BookReservationBatchPolicy::class);
    }
}
