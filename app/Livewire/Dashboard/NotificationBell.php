<?php

namespace App\Livewire\Dashboard;

use Illuminate\View\View;
use Livewire\Component;

class NotificationBell extends Component
{
    public bool $isOpen = false;

    public function toggleOpen(): void
    {
        $this->isOpen = ! $this->isOpen;
    }

    public function markAllRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
        $this->toastSuccess('All notifications marked as read.');
    }

    public function markRead(string $id): void
    {
        auth()->user()->notifications()->where('id', $id)->update(['read_at' => now()]);
    }

    public function render(): View
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->take(15)
            ->get();

        return view('livewire.dashboard.notification-bell', compact('notifications'));
    }

    // Use HasToast inline since this component doesn't use the trait
    private function toastSuccess(string $msg): void
    {
        $this->dispatch('toast', type: 'success', message: $msg);
    }
}
