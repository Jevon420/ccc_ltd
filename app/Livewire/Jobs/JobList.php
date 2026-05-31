<?php

namespace App\Livewire\Jobs;

use App\Models\Job;
use App\Traits\Livewire\HasToast;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class JobList extends Component
{
    use HasToast, WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $priorityFilter = '';

    public bool $showTrashed = false;

    public ?int $confirmingDeleteId = null;

    public ?int $confirmingRestoreId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'priorityFilter' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPriorityFilter(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        abort_unless(auth()->user()->can('jobs.delete'), 403);
        $this->confirmingDeleteId = $id;
    }

    public function deleteJob(): void
    {
        abort_unless(auth()->user()->can('jobs.delete'), 403);

        $job = Job::findOrFail($this->confirmingDeleteId);
        $job->delete();
        $this->confirmingDeleteId = null;
        $this->toastSuccess("Job {$job->reference} cancelled successfully.");
    }

    public function confirmRestore(int $id): void
    {
        abort_unless(auth()->user()->can('jobs.restore'), 403);
        $this->confirmingRestoreId = $id;
    }

    public function restoreJob(): void
    {
        abort_unless(auth()->user()->can('jobs.restore'), 403);

        $job = Job::withTrashed()->findOrFail($this->confirmingRestoreId);
        $job->restore();
        $this->confirmingRestoreId = null;
        $this->toastSuccess("Job {$job->reference} restored successfully.");
    }

    public function render(): View
    {
        abort_unless(auth()->user()->can('jobs.view'), 403);

        $jobs = Job::withTrashed()
            ->with(['client', 'serviceType'])
            ->when(! $this->showTrashed, fn ($q) => $q->whereNull('deleted_at'))
            ->when($this->showTrashed, fn ($q) => $q->whereNotNull('deleted_at'))
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('reference', 'like', "%{$this->search}%")
                    ->orWhereHas('client', fn ($q) => $q->where('name', 'like', "%{$this->search}%"));
            }))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->priorityFilter, fn ($q) => $q->where('priority', $this->priorityFilter))
            ->latest()
            ->paginate(15);

        return view('livewire.jobs.job-list', compact('jobs'));
    }
}
