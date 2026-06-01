<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use App\Traits\Livewire\HasToast;
use Illuminate\View\View;
use Livewire\Component;

class ClientShow extends Component
{
    use HasToast;

    public Client $client;

    public function toggleActive(): void
    {
        abort_unless(auth()->user()->can('clients.edit'), 403);
        $this->client->update(['is_active' => ! $this->client->is_active]);
        $this->client->refresh();
        $status = $this->client->is_active ? 'activated' : 'deactivated';
        $this->toastSuccess("Client \"{$this->client->name}\" {$status}.");
    }

    public function render(): View
    {
        abort_unless(auth()->user()->can('clients.view'), 403);

        return view('livewire.clients.client-show', [
            'jobs' => $this->client->jobs()->latest()->take(10)->get(),
            'quotes' => $this->client->quotes()->latest()->take(5)->get(),
            'invoices' => $this->client->invoices()->latest()->take(5)->get(),
        ]);
    }
}
