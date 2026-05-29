<?php

namespace App\Http\Controllers\AuditLog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('audit_logs.view');

        $query = Activity::with('causer', 'subject')->latest();

        if ($search = $request->get('search')) {
            $query->where('description', 'like', "%{$search}%");
        }

        if ($causerType = $request->get('causer_type')) {
            $query->where('causer_type', $causerType);
        }

        $logs = $query->paginate(25)->withQueryString();

        return view('dashboard.audit-log.index', compact('logs'));
    }
}
