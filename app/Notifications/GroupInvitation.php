<?php

namespace App\Notifications;

use App\Models\Group;
use App\Models\PrivateMessage;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class GroupInvitation extends Notification
{
    use Queueable;
    
    protected ?string $short_message = null;
    protected string $message;
    protected ?string $url = null;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $sender, Group $group)
    {
        $this->message = "📩 " . $sender->name . " t'a invité au groupe " . $group->name . "!";
        $this->url = '/messages/group';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // User notification preference
        if (!$notifiable->can_get_notification_from_group_invitation) {
            return [];
        }
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
