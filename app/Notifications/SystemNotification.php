<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Generic system notification — supports both in-app (database) and email.
 *
 * Usage:
 *   $user->notify(new SystemNotification('Your quote has been approved.', 'success', 'View Quote', route(...)));
 *   Notification::send($users, new SystemNotification(...));
 */
class SystemNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $message,
        public readonly string $type = 'info',  // info | success | warning | error
        public readonly ?string $actionLabel = null,
        public readonly ?string $actionUrl = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $icon = match ($this->type) {
            'success' => '✅',
            'warning' => '⚠️',
            'error' => '❌',
            default => 'ℹ️',
        };

        $mail = (new MailMessage)
            ->subject("{$icon} ".config('app.name').' — '.ucfirst($this->type))
            ->greeting('Hello, '.$notifiable->name.'!')
            ->line($this->message);

        if ($this->actionLabel && $this->actionUrl) {
            $mail->action($this->actionLabel, $this->actionUrl);
        }

        return $mail->line('Thank you for using '.config('app.name').'.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => $this->message,
            'type' => $this->type,
            'action_label' => $this->actionLabel,
            'action_url' => $this->actionUrl,
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
