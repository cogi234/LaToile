<?php

namespace App\Notifications;

use App\Models\PrivateMessage;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class MessageReceived extends Notification
{
    use Queueable;
    
    protected ?string $short_message = null;
    protected string $message;
    protected ?string $url = null;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $sender)
    {
        $this->message = $sender->name . " t'a envoyé un message!";
        $this->url = '/messages/' . $sender->id;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->message,
            'short_message' => $this->short_message,
            'url' => $this->url
        ];
    }
}
