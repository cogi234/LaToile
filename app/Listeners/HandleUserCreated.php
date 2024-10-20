<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Notifications\DatabaseNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleUserCreated
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserCreated $event): void
    {
        $event->user->notify(new DatabaseNotification('Bienvenue au réseau La Toile!'));
    }
}
