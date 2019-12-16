<?php

namespace App\Providers;

use App\prosvujusmev\Reservations\Events\ReservationApproved;
use App\prosvujusmev\Reservations\Events\ReservationCompleted;
use App\prosvujusmev\Reservations\Events\ReservationDeleted;
use App\prosvujusmev\Reservations\Listeners\SendReservationApprovedNotification;
use App\prosvujusmev\Reservations\Listeners\SendReservationCompletedNotification;
use App\prosvujusmev\Reservations\Listeners\SendReservationDeletedNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ReservationApproved::class => [
            SendReservationApprovedNotification::class,
        ],
        ReservationCompleted::class => [
            SendReservationCompletedNotification::class,
        ],
        ReservationDeleted::class => [
            SendReservationDeletedNotification::class,
        ],
 
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
