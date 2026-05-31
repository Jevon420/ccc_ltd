<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use App\Traits\Livewire\HasToast;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class ClientForm extends Component
{
    use HasToast;

    public ?int $clientId = null;

    public bool $isOpen = false;

    public string $type = 'company';

    public string $name = '';

    public string $contactPerson = '';

    public string $email = '';

    public string $phone = '';

    public string $address = '';

    public string $city = '';

    public string $notes = '';

    public bool $isActive = true;

    protected function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['company', 'individual'])],
            'name' => ['required', 'string', 'max:255'],
            'contactPerson' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'isActive' => ['boolean'],
        ];
    }

    #[On('open-create-client')]
    public function openCreate(): void
    {
        abort_unless(auth()->user()->can('clients.create'), 403);
        $this->reset(['clientId', 'name', 'contactPerson', 'email', 'phone', 'address', 'city', 'notes']);
        $this->type = 'company';
        $this->isActive = true;
        $this->isOpen = true;
    }

    #[On('open-edit-client')]
    public function openEdit(int $clientId): void
    {
        abort_unless(auth()->user()->can('clients.edit'), 403);

        $client = Client::findOrFail($clientId);
        $this->clientId = $client->id;
        $this->type = $client->type;
        $this->name = $client->name;
        $this->contactPerson = $client->contact_person ?? '';
        $this->email = $client->email ?? '';
        $this->phone = $client->phone ?? '';
        $this->address = $client->address ?? '';
        $this->city = $client->city ?? '';
        $this->notes = $client->notes ?? '';
        $this->isActive = $client->is_active;
        $this->isOpen = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'type' => $this->type,
            'name' => $this->name,
            'contact_person' => $this->contactPerson ?: null,
            'email' => $this->email ?: null,
            'phone' => $this->phone ?: null,
            'address' => $this->address ?: null,
            'city' => $this->city ?: null,
            'notes' => $this->notes ?: null,
            'is_active' => $this->isActive,
        ];

        if ($this->clientId) {
            abort_unless(auth()->user()->can('clients.edit'), 403);
            Client::findOrFail($this->clientId)->update($data);
            $this->toastSuccess("Client \"{$this->name}\" updated successfully.");
        } else {
            abort_unless(auth()->user()->can('clients.create'), 403);
            Client::create($data);
            $this->toastSuccess("Client \"{$this->name}\" created successfully.");
        }

        $this->isOpen = false;
        $this->dispatch('client-saved');
    }

    public function render(): View
    {
        return view('livewire.clients.client-form');
    }
}
