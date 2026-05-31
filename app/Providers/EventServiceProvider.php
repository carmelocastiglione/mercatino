<?php

namespace App\Providers;

use App\Events\BookSold;
use App\Events\BookReservationBatchCreated;
use App\Events\BookReservationBatchConfirmed;
use App\Events\BookReservationBatchRejected;
use App\Events\BookReservationBatchCancelled;
use App\Listeners\SendNotifications;
use App\Listeners\SendReservationNotifications;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        $this->registerBookReservationEvents();
    }

    /**
     * Register book reservation events manually.
     */
    private function registerBookReservationEvents(): void
    {
        // Register BookSold listener manually to avoid duplicate registration
        \Illuminate\Support\Facades\Event::listen(
            BookSold::class,
            SendNotifications::class
        );

        \Illuminate\Support\Facades\Event::listen(
            BookReservationBatchCreated::class,
            [SendReservationNotifications::class, 'onBatchCreated']
        );

        \Illuminate\Support\Facades\Event::listen(
            BookReservationBatchConfirmed::class,
            [SendReservationNotifications::class, 'onBatchConfirmed']
        );

        \Illuminate\Support\Facades\Event::listen(
            BookReservationBatchRejected::class,
            [SendReservationNotifications::class, 'onBatchRejected']
        );

        \Illuminate\Support\Facades\Event::listen(
            BookReservationBatchCancelled::class,
            [SendReservationNotifications::class, 'onBatchCancelled']
        );
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
