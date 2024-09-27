<?php

namespace App\Listeners;

use App\Events\UserDeleting;
use Illuminate\Support\Facades\Storage;

class HandleUserDeletion
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
    public function handle(UserDeleting $event): void
    {
        //When a user gets deleted, if it has an avatar, we delete it
        if ($event->user->avatar == null
            || !Storage::exists($event->user->avatar))
            return;
        
        Storage::delete($event->user->avatar);
    }
}
