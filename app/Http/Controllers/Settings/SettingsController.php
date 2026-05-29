<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $this->authorize('settings.view');

        // Form is handled by the Livewire SettingsForm component.
        return view('dashboard.settings.index');
    }
}
