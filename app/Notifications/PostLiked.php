<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PostLiked extends Notification
{
    use Queueable;
    
    protected ?string $short_message = null;
    protected string $message;
    protected ?string $url = null;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $liker, Post $post)
    {
        $this->message = $liker->name . " a aimé ton post!";
        $this->url = '/post/' . $post->id;
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
