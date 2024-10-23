<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class UserFollowed extends Notification
{
    use Queueable;
    
    protected ?string $short_message;
    protected string $message;
    protected ?string $url;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $follower)
    {
        $this->message = `{$follower->name} a commencé à te suivre!`;
        $this->url = url(`/user/{$follower->id}`);
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
