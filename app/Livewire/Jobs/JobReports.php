<?php

namespace App\Livewire\Jobs;

use App\Models\Job;
use App\Models\JobReport;
use App\Traits\Livewire\HasToast;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class JobReports extends Component
{
    use HasToast, WithFileUploads;

    public Job $job;

    public bool $isOpen = false;

    public ?int $editingId = null;

    public string $title = '';

    public string $description = '';

    public string $workPerformed = '';

    public string $issuesEncountered = '';

    public string $recommendations = '';

    public string $status = 'draft';

    public mixed $attachment = null;

    public ?int $confirmingDeleteId = null;

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'workPerformed' => ['nullable', 'string'],
            'issuesEncountered' => ['nullable', 'string'],
            'recommendations' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'submitted', 'approved'])],
            'attachment' => ['nullable', 'file', 'max:20480', 'mimes:pdf,doc,docx,xlsx,jpg,png'],
        ];
    }

    public function openCreate(): void
    {
        abort_unless(auth()->user()->can('job_reports.create'), 403);
        $this->reset(['editingId', 'title', 'description', 'workPerformed', 'issuesEncountered', 'recommendations', 'attachment']);
        $this->status = 'draft';
        $this->isOpen = true;
    }

    public function openEdit(int $id): void
    {
        abort_unless(auth()->user()->can('job_reports.edit'), 403);
        $report = JobReport::findOrFail($id);
        $this->editingId = $report->id;
        $this->title = $report->title;
        $this->description = $report->description;
        $this->workPerformed = $report->work_performed ?? '';
        $this->issuesEncountered = $report->issues_encountered ?? '';
        $this->recommendations = $report->recommendations ?? '';
        $this->status = $report->status;
        $this->attachment = null;
        $this->isOpen = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'job_id' => $this->job->id,
            'title' => $this->title,
            'description' => $this->description,
            'work_performed' => $this->workPerformed ?: null,
            'issues_encountered' => $this->issuesEncountered ?: null,
            'recommendations' => $this->recommendations ?: null,
            'status' => $this->status,
        ];

        if ($this->editingId) {
            abort_unless(auth()->user()->can('job_reports.edit'), 403);
            $report = JobReport::findOrFail($this->editingId);
            $report->update($data);
        } else {
            abort_unless(auth()->user()->can('job_reports.create'), 403);
            $report = JobReport::create($data);
        }

        if ($this->attachment) {
            $report->addMedia($this->attachment->getRealPath())
                ->usingFileName($this->attachment->getClientOriginalName())
                ->toMediaCollection('attachments');
        }

        $this->isOpen = false;
        $this->toastSuccess("Report \"{$this->title}\" saved.");
    }

    public function approve(int $id): void
    {
        abort_unless(auth()->user()->can('job_reports.edit'), 403);
        $report = JobReport::findOrFail($id);
        $report->update(['status' => 'approved']);
        $this->toastSuccess('Report approved.');
    }

    public function confirmDelete(int $id): void
    {
        abort_unless(auth()->user()->can('job_reports.delete'), 403);
        $this->confirmingDeleteId = $id;
    }

    public function deleteReport(): void
    {
        abort_unless(auth()->user()->can('job_reports.delete'), 403);
        JobReport::findOrFail($this->confirmingDeleteId)->delete();
        $this->confirmingDeleteId = null;
        $this->toastSuccess('Report deleted.');
    }

    public function render(): View
    {
        abort_unless(auth()->user()->can('job_reports.view'), 403);

        return view('livewire.jobs.job-reports', [
            'reports' => $this->job->reports()->with('media')->latest()->get(),
        ]);
    }
}
