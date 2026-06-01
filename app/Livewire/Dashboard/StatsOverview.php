<?php

namespace App\Livewire\Dashboard;

use App\Models\Equipment;
use App\Models\Invoice;
use App\Models\Job;
use App\Models\Quote;
use Illuminate\View\View;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

class StatsOverview extends Component
{
    public function render(): View
    {
        return view('livewire.dashboard.stats-overview', [
            'stats' => $this->getStats(),
            'recentActivity' => $this->getRecentActivity(),
        ]);
    }

    private function getStats(): array
    {
        return [
            'active_jobs' => Job::whereNull('deleted_at')->whereIn('status', ['pending', 'scheduled', 'in_progress'])->count(),
            'pending_quotes' => Quote::whereNull('deleted_at')->whereIn('status', ['draft', 'sent'])->count(),
            'unpaid_invoices' => Invoice::whereNull('deleted_at')->whereNotIn('status', ['paid', 'cancelled'])->count(),
            'overdue_invoices' => Invoice::overdue()->count(),
            'equipment_issues' => Equipment::whereNull('deleted_at')->where('status', 'maintenance')->count(),
            'completed_this_month' => Job::whereNull('deleted_at')->where('status', 'completed')->whereMonth('completed_date', now()->month)->whereYear('completed_date', now()->year)->count(),
        ];
    }

    private function getRecentActivity()
    {
        return Activity::with('causer')
            ->latest()
            ->take(10)
            ->get();
    }

    public function refresh(): void {} // triggers re-render
}
