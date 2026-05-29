<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

/**
 * Dashboard Stats Overview
 *
 * Live-refreshable dashboard cards. Real model counts will be wired up in Phase 2
 * as each business module is built. Currently shows placeholder zeros.
 *
 * Refreshes every 60 seconds automatically.
 */
class StatsOverview extends Component
{
    public int $refreshIntervalSeconds = 60;

    public function render()
    {
        return view('livewire.dashboard.stats-overview', [
            'stats' => $this->getStats(),
            'recentActivity' => $this->getRecentActivity(),
        ]);
    }

    private function getStats(): array
    {
        // ---------------------------------------------------------------
        // Phase 2: replace each 0 with the real Eloquent query
        // e.g. 'pending_job_requests' => JobRequest::pending()->count()
        // ---------------------------------------------------------------
        return [
            'pending_job_requests' => 0, // Phase 2: JobRequest::pending()->count()
            'active_jobs'          => 0, // Phase 2: Job::active()->count()
            'pending_quotes'       => 0, // Phase 2: Quote::pending()->count()
            'unpaid_invoices'      => 0, // Phase 2: Invoice::unpaid()->count()
            'equipment_issues'     => 0, // Phase 2: Equipment::hasIssue()->count()
            'completed_this_month' => 0, // Phase 2: Job::completedThisMonth()->count()
        ];
    }

    private function getRecentActivity()
    {
        return Activity::with('causer')
            ->latest()
            ->take(10)
            ->get();
    }

    /**
     * Allow manual refresh from the front-end.
     */
    public function refresh(): void
    {
        // Livewire will automatically re-render after this is called
    }
}
