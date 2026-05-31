<?php

namespace App\Livewire\Jobs;

use App\Models\Client;
use App\Models\Job;
use App\Models\ServiceType;
use App\Traits\Livewire\HasToast;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

class JobForm extends Component
{
    use HasToast;

    protected $listeners = [
        'open-create' => 'openCreate',
        'open-edit' => 'openEdit',
    ];

    public ?int $jobId = null;

    public bool $isOpen = false;

    public string $title = '';

    public string $description = '';

    public string $location = '';

    public string $status = 'pending';

    public string $priority = 'normal';

    public string $scheduledDate = '';

    public string $scheduledTime = '';

    public string $notes = '';

    public ?int $clientId = null;

    public ?int $serviceTypeId = null;

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'clientId' => ['required', 'exists:clients,id'],
            'serviceTypeId' => ['nullable', 'exists:service_types,id'],
            'description' => ['nullable', 'string', 'max:5000'],
            'location' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::in(['pending', 'scheduled', 'in_progress', 'completed', 'cancelled'])],
            'priority' => ['required', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'scheduledDate' => ['nullable', 'date'],
            'scheduledTime' => ['nullable', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function openCreate(): void
    {
        abort_unless(auth()->user()->can('jobs.create'), 403);
        $this->reset(['jobId', 'title', 'description', 'location', 'scheduledDate', 'scheduledTime', 'notes', 'clientId', 'serviceTypeId']);
        $this->status = 'pending';
        $this->priority = 'normal';
        $this->isOpen = true;
    }

    public function openEdit(int $jobId): void
    {
        abort_unless(auth()->user()->can('jobs.edit'), 403);

        $job = Job::findOrFail($jobId);
        $this->jobId = $job->id;
        $this->title = $job->title;
        $this->description = $job->description ?? '';
        $this->location = $job->location ?? '';
        $this->status = $job->status;
        $this->priority = $job->priority;
        $this->scheduledDate = $job->scheduled_date?->format('Y-m-d') ?? '';
        $this->scheduledTime = $job->scheduled_time ?? '';
        $this->notes = $job->notes ?? '';
        $this->clientId = $job->client_id;
        $this->serviceTypeId = $job->service_type_id;
        $this->isOpen = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'client_id' => $this->clientId,
            'service_type_id' => $this->serviceTypeId ?: null,
            'title' => $this->title,
            'description' => $this->description ?: null,
            'location' => $this->location ?: null,
            'status' => $this->status,
            'priority' => $this->priority,
            'scheduled_date' => $this->scheduledDate ?: null,
            'scheduled_time' => $this->scheduledTime ?: null,
            'notes' => $this->notes ?: null,
            'completed_date' => $this->status === 'completed' ? now() : null,
        ];

        if ($this->jobId) {
            abort_unless(auth()->user()->can('jobs.edit'), 403);
            $job = Job::findOrFail($this->jobId);
            $job->update($data);
            $this->toastSuccess("Job {$job->reference} updated successfully.");
        } else {
            abort_unless(auth()->user()->can('jobs.create'), 403);
            $job = Job::create($data);
            $this->toastSuccess("Job {$job->reference} created successfully.");
        }

        $this->isOpen = false;
        $this->dispatch('job-saved');
    }

    public function render(): View
    {
        return view('livewire.jobs.job-form', [
            'clients' => Client::active()->orderBy('name')->get(),
            'serviceTypes' => ServiceType::active()->orderBy('name')->get(),
        ]);
    }
}
