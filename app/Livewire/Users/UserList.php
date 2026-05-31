<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Traits\Livewire\HasToast;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UserList extends Component
{
    use HasToast, WithPagination;

    public string $search = '';

    public string $roleFilter = '';

    public string $statusFilter = '';

    public ?int $confirmingDeleteId = null;

    public ?int $confirmingRestoreId = null;

    public bool $showTrashed = false;

    // -------------------------------------------------------------------------
    // Open form helpers — dispatch events to the embedded UserForm component
    // -------------------------------------------------------------------------

    public function openCreate(): void
    {
        $this->dispatch('open-create-user');
    }

    public function openEdit(int $userId): void
    {
        $this->dispatch('open-edit-user', userId: $userId);
    }

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $userId): void
    {
        abort_unless(auth()->user()->can('users.delete'), 403);
        $this->confirmingDeleteId = $userId;
    }

    public function deleteUser(): void
    {
        abort_unless(auth()->user()->can('users.delete'), 403);

        $user = User::findOrFail($this->confirmingDeleteId);

        if ($user->id === auth()->id()) {
            $this->toastError('You cannot deactivate your own account.');
            $this->confirmingDeleteId = null;

            return;
        }

        $user->delete();
        $this->confirmingDeleteId = null;
        $this->toastSuccess("User {$user->name} has been deactivated.");
    }

    public function confirmRestore(int $userId): void
    {
        abort_unless(auth()->user()->can('users.restore'), 403);
        $this->confirmingRestoreId = $userId;
    }

    public function restoreUser(): void
    {
        abort_unless(auth()->user()->can('users.restore'), 403);

        $user = User::withTrashed()->findOrFail($this->confirmingRestoreId);
        $user->restore();
        $this->confirmingRestoreId = null;
        $this->toastSuccess("User {$user->name} has been restored.");
    }

    public function render(): View
    {
        abort_unless(auth()->user()->can('users.view'), 403);

        $query = User::withTrashed()
            ->with('roles')
            ->when(! $this->showTrashed, fn ($q) => $q->whereNull('deleted_at'))
            ->when($this->showTrashed, fn ($q) => $q->whereNotNull('deleted_at'))
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->when($this->roleFilter, fn ($q) => $q->whereHas('roles', fn ($q) => $q->where('name', $this->roleFilter)))
            ->when($this->statusFilter === 'active', fn ($q) => $q->where('is_active', true))
            ->when($this->statusFilter === 'inactive', fn ($q) => $q->where('is_active', false))
            ->latest();

        return view('livewire.users.user-list', [
            'users' => $query->paginate(15),
            'roles' => Role::orderBy('name')->pluck('name'),
        ]);
    }
}
