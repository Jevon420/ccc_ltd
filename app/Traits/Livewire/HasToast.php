<?php

namespace App\Traits\Livewire;

trait HasToast
{
    /**
     * Dispatch a toast notification to the frontend.
     *
     * @param  'success'|'error'|'warning'|'info'  $type
     */
    public function toast(string $type, string $message, int $duration = 4000): void
    {
        $this->dispatch('toast', type: $type, message: $message, duration: $duration);
    }

    public function toastSuccess(string $message): void
    {
        $this->toast('success', $message);
    }

    public function toastError(string $message): void
    {
        $this->toast('error', $message, 5000);
    }

    public function toastWarning(string $message): void
    {
        $this->toast('warning', $message);
    }

    public function toastInfo(string $message): void
    {
        $this->toast('info', $message);
    }
}
