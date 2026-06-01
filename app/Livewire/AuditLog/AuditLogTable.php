<?php

namespace App\Livewire\AuditLog;

use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class AuditLogTable extends Component
{
    use WithPagination;

    public string $search = '';

    public string $event = '';

    public string $causer = '';

    public ?int $selectedId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'event' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingEvent(): void
    {
        $this->resetPage();
    }

    public function viewDetail(int $id): void
    {
        $this->selectedId = $id;
    }

    public function closeDetail(): void
    {
        $this->selectedId = null;
    }

    public function render(): View
    {
        abort_unless(auth()->user()->can('audit_logs.view'), 403);

        $logs = Activity::with('causer', 'subject')
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('description', 'like', "%{$this->search}%")
                    ->orWhere('subject_type', 'like', "%{$this->search}%");
            }))
            ->when($this->event, fn ($q) => $q->where('event', $this->event))
            ->latest()
            ->paginate(25);

        $detail = $this->selectedId ? Activity::with('causer', 'subject')->find($this->selectedId) : null;

        $events = Activity::distinct()->pluck('event')->filter()->sort()->values();

        return view('livewire.audit-log.audit-log-table', compact('logs', 'detail', 'events'));
    }
}
