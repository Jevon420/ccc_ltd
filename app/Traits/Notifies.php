<?php

namespace App\Traits;

use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;

/**
 * Helper methods for dispatching SystemNotifications to relevant staff.
 * Import into Livewire components or service classes.
 */
trait Notifies
{
    /**
     * Notify all users with a given permission (queued, non-blocking).
     */
    protected function notifyUsersWithPermission(
        string $permission,
        string $message,
        string $type = 'info',
        ?string $actionLabel = null,
        ?string $actionUrl = null,
    ): void {
        $users = User::whereNull('deleted_at')
            ->where('is_active', true)
            ->get()
            ->filter(fn (User $user) => $user->can($permission));

        if ($users->isEmpty()) {
            return;
        }

        Notification::send(
            $users,
            new SystemNotification($message, $type, $actionLabel, $actionUrl)
        );
    }

    /**
     * Notify a single user (queued, non-blocking).
     */
    protected function notifyUser(
        User $user,
        string $message,
        string $type = 'info',
        ?string $actionLabel = null,
        ?string $actionUrl = null,
    ): void {
        $user->notify(new SystemNotification($message, $type, $actionLabel, $actionUrl));
    }
}
