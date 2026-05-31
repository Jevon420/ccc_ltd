<?php

namespace App\Livewire\ServiceTypes;

use App\Models\ServiceType;
use App\Traits\Livewire\HasToast;
use Illuminate\View\View;
use Livewire\Component;

class ServiceTypeList extends Component
{
    use HasToast;

    // Create form
    public bool $showCreateForm = false;

    public string $newName = '';

    public string $newDescription = '';

    // Edit inline
    public ?int $editingId = null;

    public string $editName = '';

    public string $editDescription = '';

    // Confirm delete
    public ?int $confirmingDeleteId = null;

    protected function rules(): array
    {
        return [
            'newName' => ['required', 'string', 'max:255', 'unique:service_types,name'],
            'newDescription' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function editRules(): array
    {
        return [
            'editName' => ['required', 'string', 'max:255', "unique:service_types,name,{$this->editingId}"],
            'editDescription' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function create(): void
    {
        abort_unless(auth()->user()->can('service_types.create'), 403);

        $this->validateOnly('newName', $this->rules());
        $this->validateOnly('newDescription', $this->rules());

        ServiceType::create([
            'name' => $this->newName,
            'description' => $this->newDescription ?: null,
        ]);

        $this->reset(['newName', 'newDescription', 'showCreateForm']);
        $this->toastSuccess('Service type created successfully.');
    }

    public function startEdit(int $id): void
    {
        abort_unless(auth()->user()->can('service_types.edit'), 403);

        $type = ServiceType::findOrFail($id);
        $this->editingId = $type->id;
        $this->editName = $type->name;
        $this->editDescription = $type->description ?? '';
    }

    public function saveEdit(): void
    {
        abort_unless(auth()->user()->can('service_types.edit'), 403);

        $this->validate($this->editRules());

        ServiceType::findOrFail($this->editingId)->update([
            'name' => $this->editName,
            'description' => $this->editDescription ?: null,
        ]);

        $this->reset(['editingId', 'editName', 'editDescription']);
        $this->toastSuccess('Service type updated successfully.');
    }

    public function toggleActive(int $id): void
    {
        abort_unless(auth()->user()->can('service_types.edit'), 403);

        $type = ServiceType::findOrFail($id);
        $type->update(['is_active' => ! $type->is_active]);

        $status = $type->fresh()->is_active ? 'activated' : 'deactivated';
        $this->toastInfo("Service type \"{$type->name}\" {$status}.");
    }

    public function confirmDelete(int $id): void
    {
        abort_unless(auth()->user()->can('service_types.delete'), 403);
        $this->confirmingDeleteId = $id;
    }

    public function deleteServiceType(): void
    {
        abort_unless(auth()->user()->can('service_types.delete'), 403);

        $type = ServiceType::findOrFail($this->confirmingDeleteId);
        $type->delete();
        $this->confirmingDeleteId = null;
        $this->toastSuccess("Service type \"{$type->name}\" deleted.");
    }

    public function render(): View
    {
        abort_unless(auth()->user()->can('service_types.view'), 403);

        return view('livewire.service-types.service-type-list', [
            'serviceTypes' => ServiceType::orderBy('name')->get(),
        ]);
    }
}
