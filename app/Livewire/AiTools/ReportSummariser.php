<?php

namespace App\Livewire\AiTools;

use App\Jobs\GenerateReportSummary as GenerateReportSummaryJob;
use App\Models\Setting;
use App\Traits\Livewire\HasToast;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;

class ReportSummariser extends Component
{
    use HasToast;

    public string $reportText = '';

    public string $summary = '';

    public bool $generating = false;

    public ?string $cacheKey = null;

    public ?string $errorMessage = null;

    protected function rules(): array
    {
        return [
            'reportText' => ['required', 'string', 'min:50'],
        ];
    }

    public function summarise(): void
    {
        abort_unless(auth()->user()->can('ai_tools.use'), 403);
        abort_unless((bool) Setting::get('ai_features_enabled', false), 403);
        abort_unless((bool) Setting::get('ai_report_summaries', true), 403);

        $this->validate();

        $this->generating = true;
        $this->summary = '';
        $this->errorMessage = null;
        $this->cacheKey = 'report_summary_'.Str::uuid();

        GenerateReportSummaryJob::dispatch($this->cacheKey, $this->reportText);
        $this->toastInfo('Generating summary in background…');
    }

    public function checkSummary(): void
    {
        if (! $this->generating || ! $this->cacheKey) {
            return;
        }

        $result = Cache::get($this->cacheKey);

        if ($result && ($result['done'] ?? false)) {
            $this->generating = false;

            if (isset($result['error'])) {
                $this->errorMessage = 'Summary failed: '.$result['error'];
                $this->toastError('AI summary failed. Please try again.');
            } else {
                $this->summary = $result['summary'] ?? '';
                $this->toastSuccess('Report summary ready!');
            }

            Cache::forget($this->cacheKey);
            $this->cacheKey = null;
        }
    }

    public function render(): View
    {
        return view('livewire.ai-tools.report-summariser');
    }
}
