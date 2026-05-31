<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use App\Traits\Livewire\HasToast;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class ClientList extends Component
{
    use HasToast, WithPagination;

    public string $search = '';

    public string $typeFilter = '';

    public string $statusFilter = '';

    public bool $showTrashed = false;

    public ?int $confirmingDeleteId = null;

    public ?int $confirmingRestoreId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'typeFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        abort_unless(auth()->user()->can('clients.delete'), 403);
        $this->confirmingDeleteId = $id;
    }

    public function deleteClient(): void
    {
        abort_unless(auth()->user()->can('clients.delete'), 403);

        $client = Client::findOrFail($this->confirmingDeleteId);
        $client->delete();
        $this->confirmingDeleteId = null;
        $this->toastSuccess("Client \"{$client->name}\" archived successfully.");
    }

    public function confirmRestore(int $id): void
    {
        abort_unless(auth()->user()->can('clients.restore'), 403);
        $this->confirmingRestoreId = $id;
    }

    public function restoreClient(): void
    {
        abort_unless(auth()->user()->can('clients.restore'), 403);

        $client = Client::withTrashed()->findOrFail($this->confirmingRestoreId);
        $client->restore();
        $this->confirmingRestoreId = null;
        $this->toastSuccess("Client \"{$client->name}\" restored successfully.");
    }

    public function render(): View
    {
        abort_unless(auth()->user()->can('clients.view'), 403);

        $clients = Client::withTrashed()
            ->when(! $this->showTrashed, fn ($q) => $q->whereNull('deleted_at'))
            ->when($this->showTrashed, fn ($q) => $q->whereNotNull('deleted_at'))
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('contact_person', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%");
            }))
            ->when($this->typeFilter, fn ($q) => $q->where('type', $this->typeFilter))
            ->when($this->statusFilter === 'active', fn ($q) => $q->where('is_active', true))
            ->when($this->statusFilter === 'inactive', fn ($q) => $q->where('is_active', false))
            ->latest()
            ->paginate(15);

        return view('livewire.clients.client-list', compact('clients'));
    }
}
