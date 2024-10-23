<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ReportConfirmation extends Notification
{
    use Queueable;
    
    protected ?string $short_message = null;
    protected string $message;
    protected ?string $url = null;

    /**
     * Create a new notification instance.
     */
    public function __construct(Post $post, string $reason)
    {
        $this->message = '✅ Votre signalement pour "' . $reason . '" à bien été envoyé';
        $this->short_message = "✅ Votre signalement à bien été envoyé";
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
