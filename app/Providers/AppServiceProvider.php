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

        // Define authorization gates
        Gate::define('admin', function ($user) {
            return $user->role === 'admin';
        });

        // Register custom notification channel for handling custom notifications table
        $this->app->make(\Illuminate\Notifications\ChannelManager::class)->extend('custom', function ($app) {
            return new class {
                public function send($notifiable, $notification)
                {
                    if (method_exists($notification, 'toCustom')) {
                        return $notification->toCustom($notifiable);
                    }
                }
            };
        });
    }
}
