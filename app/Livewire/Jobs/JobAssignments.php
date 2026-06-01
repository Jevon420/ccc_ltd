<?php

namespace App\Livewire\Jobs;

use App\Models\Job;
use App\Models\JobAssignment;
use App\Models\User;
use App\Traits\Livewire\HasToast;
use App\Traits\Notifies;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

class JobAssignments extends Component
{
    use HasToast, Notifies;

    public Job $job;

    public bool $isOpen = false;

    public ?int $userId = null;

    public string $role = 'assistant';

    public string $assignedDate = '';

    public string $notes = '';

    public ?int $confirmingRemoveId = null;

    protected function rules(): array
    {
        return [
            'userId' => ['required', 'exists:users,id'],
            'role' => ['required', Rule::in(['lead', 'assistant', 'driver', 'supervisor'])],
            'assignedDate' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function openAssign(): void
    {
        abort_unless(auth()->user()->can('job_assignments.create'), 403);
        $this->reset(['userId', 'assignedDate', 'notes']);
        $this->role = 'assistant';
        $this->isOpen = true;
    }

    public function assign(): void
    {
        abort_unless(auth()->user()->can('job_assignments.create'), 403);

        $this->validate();

        // Check not already assigned
        $exists = JobAssignment::where('job_id', $this->job->id)
            ->where('user_id', $this->userId)
            ->exists();

        if ($exists) {
            $this->toastError('This staff member is already assigned to this job.');

            return;
        }

        JobAssignment::create([
            'job_id' => $this->job->id,
            'user_id' => $this->userId,
            'role' => $this->role,
            'assigned_date' => $this->assignedDate ?: today(),
            'notes' => $this->notes ?: null,
        ]);

        $assignedUser = User::find($this->userId);
        $this->isOpen = false;
        $this->toastSuccess("{$assignedUser?->name} assigned to job {$this->job->reference}.");

        // Notify the assigned user
        if ($assignedUser) {
            $this->notifyUser(
                $assignedUser,
                "You have been assigned to job {$this->job->reference}: {$this->job->title}.",
                'info',
                'View Job',
                route('dashboard.jobs.show', $this->job)
            );
        }
    }

    public function confirmRemove(int $id): void
    {
        abort_unless(auth()->user()->can('job_assignments.delete'), 403);
        $this->confirmingRemoveId = $id;
    }

    public function removeAssignment(): void
    {
        abort_unless(auth()->user()->can('job_assignments.delete'), 403);
        $assignment = JobAssignment::findOrFail($this->confirmingRemoveId);
        $name = $assignment->user?->name ?? 'Staff member';
        $assignment->delete();
        $this->confirmingRemoveId = null;
        $this->toastSuccess("{$name} removed from job {$this->job->reference}.");
    }

    public function render(): View
    {
        abort_unless(auth()->user()->can('job_assignments.view'), 403);

        $assignments = $this->job->assignments()->with('user')->get();
        $assignedIds = $assignments->pluck('user_id')->toArray();

        $availableStaff = User::whereNull('deleted_at')
            ->where('is_active', true)
            ->whereNotIn('id', $assignedIds)
            ->orderBy('name')
            ->get()
            ->filter(fn (User $u) => $u->hasAnyRole(['Supervisor', 'Field Worker', 'Driver', 'Operations Manager']));

        return view('livewire.jobs.job-assignments', compact('assignments', 'availableStaff'));
    }
}
