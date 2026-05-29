<?php

namespace App\Http\Controllers\SystemHealth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\View\View;

class SystemHealthController extends Controller
{
    public function index(): View
    {
        $this->authorize('system_health.view');

        $health = [
            'app_version'   => app()->version(),
            'app_env'       => config('app.env'),
            'app_debug'     => config('app.debug'),
            'db_status'     => $this->checkDatabase(),
            'queue_driver'  => config('queue.default'),
            'cache_driver'  => config('cache.default'),
            'storage_disk'  => config('filesystems.default'),
            'failed_jobs'   => $this->getFailedJobCount(),
            'php_version'   => PHP_VERSION,
            'laravel_version' => app()->version(),
        ];

        return view('dashboard.system-health.index', compact('health'));
    }

    private function checkDatabase(): string
    {
        try {
            DB::connection()->getPdo();

            return 'connected';
        } catch (\Throwable) {
            return 'error';
        }
    }

    private function getFailedJobCount(): int
    {
        try {
            return DB::table('failed_jobs')->count();
        } catch (\Throwable) {
            return 0;
        }
    }
}
