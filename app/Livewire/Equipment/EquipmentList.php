<?php

namespace App\Livewire\Equipment;

use App\Models\Equipment;
use App\Traits\Livewire\HasToast;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class EquipmentList extends Component
{
    use HasToast, WithPagination;

    public string $search = '';

    public string $typeFilter = '';

    public string $statusFilter = '';

    public bool $showTrashed = false;

    // Form
    public bool $isOpen = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $type = 'other';

    public string $serialNumber = '';

    public string $makeModel = '';

    public string $purchaseDate = '';

    public string $condition = 'good';

    public string $status = 'active';

    public string $notes = '';

    public ?int $confirmingDeleteId = null;

    public ?int $confirmingRestoreId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'typeFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:vehicle,machinery,tool,ppe,other'],
            'serialNumber' => ['nullable', 'string', 'max:100'],
            'makeModel' => ['nullable', 'string', 'max:255'],
            'purchaseDate' => ['nullable', 'date'],
            'condition' => ['required', 'in:excellent,good,fair,poor'],
            'status' => ['required', 'in:active,maintenance,retired'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        abort_unless(auth()->user()->can('equipment.create'), 403);
        $this->reset(['editingId', 'name', 'serialNumber', 'makeModel', 'purchaseDate', 'notes']);
        $this->type = 'other';
        $this->condition = 'good';
        $this->status = 'active';
        $this->isOpen = true;
    }

    public function openEdit(int $id): void
    {
        abort_unless(auth()->user()->can('equipment.edit'), 403);
        $e = Equipment::findOrFail($id);
        $this->editingId = $e->id;
        $this->name = $e->name;
        $this->type = $e->type;
        $this->serialNumber = $e->serial_number ?? '';
        $this->makeModel = $e->make_model ?? '';
        $this->purchaseDate = $e->purchase_date?->format('Y-m-d') ?? '';
        $this->condition = $e->condition;
        $this->status = $e->status;
        $this->notes = $e->notes ?? '';
        $this->isOpen = true;
    }

    public function save(): void
    {
        $this->validate();
        $data = [
            'name' => $this->name,
            'type' => $this->type,
            'serial_number' => $this->serialNumber ?: null,
            'make_model' => $this->makeModel ?: null,
            'purchase_date' => $this->purchaseDate ?: null,
            'condition' => $this->condition,
            'status' => $this->status,
            'notes' => $this->notes ?: null,
        ];

        if ($this->editingId) {
            abort_unless(auth()->user()->can('equipment.edit'), 403);
            Equipment::findOrFail($this->editingId)->update($data);
            $this->toastSuccess("Equipment \"{$this->name}\" updated.");
        } else {
            abort_unless(auth()->user()->can('equipment.create'), 403);
            Equipment::create($data);
            $this->toastSuccess("Equipment \"{$this->name}\" added.");
        }

        $this->isOpen = false;
    }

    public function confirmDelete(int $id): void
    {
        abort_unless(auth()->user()->can('equipment.delete'), 403);
        $this->confirmingDeleteId = $id;
    }

    public function deleteEquipment(): void
    {
        abort_unless(auth()->user()->can('equipment.delete'), 403);
        $e = Equipment::findOrFail($this->confirmingDeleteId);
        $e->delete();
        $this->confirmingDeleteId = null;
        $this->toastSuccess("Equipment \"{$e->name}\" archived.");
    }

    public function confirmRestore(int $id): void
    {
        abort_unless(auth()->user()->can('equipment.restore'), 403);
        $this->confirmingRestoreId = $id;
    }

    public function restoreEquipment(): void
    {
        abort_unless(auth()->user()->can('equipment.restore'), 403);
        $e = Equipment::withTrashed()->findOrFail($this->confirmingRestoreId);
        $e->restore();
        $this->confirmingRestoreId = null;
        $this->toastSuccess("Equipment \"{$e->name}\" restored.");
    }

    public function render(): View
    {
        abort_unless(auth()->user()->can('equipment.view'), 403);

        $equipment = Equipment::withTrashed()
            ->when(! $this->showTrashed, fn ($q) => $q->whereNull('deleted_at'))
            ->when($this->showTrashed, fn ($q) => $q->whereNotNull('deleted_at'))
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('serial_number', 'like', "%{$this->search}%")
                    ->orWhere('make_model', 'like', "%{$this->search}%");
            }))
            ->when($this->typeFilter, fn ($q) => $q->where('type', $this->typeFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->orderBy('name')
            ->paginate(20);

        return view('livewire.equipment.equipment-list', compact('equipment'));
    }
}
