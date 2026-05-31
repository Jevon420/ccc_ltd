<?php

namespace App\Livewire\Jobs;

use App\Models\Job;
use App\Traits\Livewire\HasToast;
use Illuminate\View\View;
use Livewire\Component;

class JobShow extends Component
{
    use HasToast;

    public Job $job;

    public function updateStatus(string $status): void
    {
        abort_unless(auth()->user()->can('jobs.edit'), 403);

        $this->job->update([
            'status' => $status,
            'completed_date' => $status === 'completed' ? now() : $this->job->completed_date,
        ]);

        $this->job->refresh();
        $this->toastSuccess('Job status updated to '.ucfirst(str_replace('_', ' ', $status)).'.');
    }

    public function render(): View
    {
        abort_unless(auth()->user()->can('jobs.view'), 403);

        return view('livewire.jobs.job-show');
    }
}
