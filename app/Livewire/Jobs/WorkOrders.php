<?php

namespace App\Livewire\Jobs;

use App\Models\Job;
use App\Models\WorkOrder;
use App\Traits\Livewire\HasToast;
use App\Traits\Notifies;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

class WorkOrders extends Component
{
    use HasToast, Notifies;

    public Job $job;

    public bool $isOpen = false;

    public ?int $editingId = null;

    public string $title = '';

    public string $description = '';

    public string $status = 'pending';

    public string $priority = 'normal';

    public string $dueDate = '';

    public ?int $completingId = null;

    public string $completionNotes = '';

    public ?int $confirmingDeleteId = null;

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],
            'priority' => ['required', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'dueDate' => ['nullable', 'date'],
        ];
    }

    public function openCreate(): void
    {
        abort_unless(auth()->user()->can('work_orders.create'), 403);
        $this->reset(['editingId', 'title', 'description', 'dueDate', 'completionNotes']);
        $this->status = 'pending';
        $this->priority = 'normal';
        $this->isOpen = true;
    }

    public function openEdit(int $id): void
    {
        abort_unless(auth()->user()->can('work_orders.edit'), 403);
        $wo = WorkOrder::findOrFail($id);
        $this->editingId = $wo->id;
        $this->title = $wo->title;
        $this->description = $wo->description ?? '';
        $this->status = $wo->status;
        $this->priority = $wo->priority;
        $this->dueDate = $wo->due_date?->format('Y-m-d') ?? '';
        $this->isOpen = true;
    }

    public function save(): void
    {
        $this->validate();
        $data = [
            'job_id' => $this->job->id,
            'title' => $this->title,
            'description' => $this->description ?: null,
            'status' => $this->status,
            'priority' => $this->priority,
            'due_date' => $this->dueDate ?: null,
        ];

        if ($this->editingId) {
            abort_unless(auth()->user()->can('work_orders.edit'), 403);
            WorkOrder::findOrFail($this->editingId)->update($data);
            $this->toastSuccess("Work order \"{$this->title}\" updated.");
        } else {
            abort_unless(auth()->user()->can('work_orders.create'), 403);
            WorkOrder::create($data);
            $this->toastSuccess("Work order \"{$this->title}\" created.");
        }

        $this->isOpen = false;
    }

    public function openComplete(int $id): void
    {
        abort_unless(auth()->user()->can('work_orders.complete'), 403);
        $this->completingId = $id;
        $this->completionNotes = '';
    }

    public function complete(): void
    {
        abort_unless(auth()->user()->can('work_orders.complete'), 403);

        $wo = WorkOrder::findOrFail($this->completingId);
        $wo->update([
            'status' => 'completed',
            'completed_at' => now(),
            'completion_notes' => $this->completionNotes ?: null,
        ]);

        $this->completingId = null;
        $this->toastSuccess("Work order \"{$wo->title}\" marked complete.");

        if ($this->job->workOrders()->whereNotIn('status', ['completed', 'cancelled'])->doesntExist()) {
            $this->notifyUsersWithPermission(
                'jobs.edit',
                "All work orders for job {$this->job->reference} are now complete.",
                'success',
                'View Job',
                route('dashboard.jobs.show', $this->job)
            );
        }
    }

    public function confirmDelete(int $id): void
    {
        abort_unless(auth()->user()->can('work_orders.delete'), 403);
        $this->confirmingDeleteId = $id;
    }

    public function deleteWorkOrder(): void
    {
        abort_unless(auth()->user()->can('work_orders.delete'), 403);
        WorkOrder::findOrFail($this->confirmingDeleteId)->delete();
        $this->confirmingDeleteId = null;
        $this->toastSuccess('Work order deleted.');
    }

    public function render(): View
    {
        abort_unless(auth()->user()->can('work_orders.view'), 403);

        return view('livewire.jobs.work-orders', [
            'workOrders' => $this->job->workOrders()->get(),
        ]);
    }
}
