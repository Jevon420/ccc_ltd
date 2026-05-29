<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Stats and activity are now handled by the Livewire StatsOverview component.
        // Pass only the data the Blade wrapper needs here.
        return view('dashboard.index');
    }
}
