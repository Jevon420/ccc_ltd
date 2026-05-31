<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Traits\Livewire\HasToast;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class UserForm extends Component
{
    use HasToast;

    protected $listeners = [
        'open-create' => 'openCreate',
        'open-edit' => 'openEdit',
    ];

    public ?int $userId = null;

    public bool $isOpen = false;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $position = '';

    public string $role = '';

    public string $password = '';

    public bool $isActive = true;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->userId)],
            'phone' => ['nullable', 'string', 'max:50'],
            'position' => ['nullable', 'string', 'max:255'],
            'role' => ['required', 'string', Rule::exists('roles', 'name')],
            'password' => $this->userId ? ['nullable', 'string', 'min:8'] : ['required', 'string', 'min:8'],
            'isActive' => ['boolean'],
        ];
    }

    public function openCreate(): void
    {
        abort_unless(auth()->user()->can('users.create'), 403);
        $this->reset(['userId', 'name', 'email', 'phone', 'position', 'role', 'password']);
        $this->isActive = true;
        $this->isOpen = true;
    }

    public function openEdit(int $userId): void
    {
        abort_unless(auth()->user()->can('users.edit'), 403);

        $user = User::findOrFail($userId);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->position = $user->position ?? '';
        $this->role = $user->getRoleNames()->first() ?? '';
        $this->isActive = $user->is_active;
        $this->password = '';
        $this->isOpen = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->userId) {
            abort_unless(auth()->user()->can('users.edit'), 403);

            $user = User::findOrFail($this->userId);
            $user->update([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone ?: null,
                'position' => $this->position ?: null,
                'is_active' => $this->isActive,
            ]);

            if ($this->password) {
                $user->update(['password' => Hash::make($this->password)]);
            }

            $user->syncRoles([$this->role]);
            $this->toastSuccess("User {$user->name} updated successfully.");
        } else {
            abort_unless(auth()->user()->can('users.create'), 403);

            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone ?: null,
                'position' => $this->position ?: null,
                'is_active' => $this->isActive,
                'password' => Hash::make($this->password),
            ]);

            $user->assignRole($this->role);
            $this->toastSuccess("User {$user->name} created successfully.");
        }

        $this->isOpen = false;
        $this->dispatch('userSaved');
    }

    public function render(): View
    {
        return view('livewire.users.user-form', [
            'roles' => Role::orderBy('name')->pluck('name'),
        ]);
    }
}
