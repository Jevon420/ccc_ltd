<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Generic system notification.
 * Used for admin alerts, job updates, approval requests, etc.
 *
 * Phase 2: Extend with specific notification types per business event.
 * Phase 2: Add database channel for in-app notification centre.
 *
 * Usage:
 *   $user->notify(new SystemNotification('Your quote has been approved.', 'info'));
 */
class SystemNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $message,
        public readonly string $type = 'info',   // info | success | warning | error
        public readonly ?string $actionLabel = null,
        public readonly ?string $actionUrl = null,
    ) {}

    public function via(object $notifiable): array
    {
        // Phase 2: add 'database' for in-app notification bell
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject(config('app.name').' — '.ucfirst($this->type))
            ->greeting('Hello, '.$notifiable->name.'!')
            ->line($this->message);

        if ($this->actionLabel && $this->actionUrl) {
            $mail->action($this->actionLabel, $this->actionUrl);
        }

        return $mail->line('Thank you for using '.config('app.name').'.');
    }

    public function toArray(object $notifiable): array
    {
        // Stored in notifications table when 'database' channel is added
        return [
            'message'      => $this->message,
            'type'         => $this->type,
            'action_label' => $this->actionLabel,
            'action_url'   => $this->actionUrl,
        ];
    }
}
