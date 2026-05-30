<?php

use App\Http\Controllers\AuditLog\AuditLogController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Public\PublicController;
use App\Http\Controllers\Public\SitemapController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\SystemHealth\SystemHealthController;
use App\Services\WiPay\WiPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CCC Ops — Web Routes
| Phase 1: Public site + Auth + Core Dashboard shell
| Phase 2: Add clients, jobs, quotes, invoices, payments, equipment…
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::group([], function () {
    Route::get('/', [PublicController::class, 'home'])->name('home');
    Route::get('/about', [PublicController::class, 'about'])->name('about');
    Route::get('/services', [PublicController::class, 'services'])->name('services');
    Route::get('/projects', [PublicController::class, 'projects'])->name('projects');
    Route::get('/contact', [PublicController::class, 'contact'])->name('contact');
    Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
});

/*
|--------------------------------------------------------------------------
| Auth Routes (guests only)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated Dashboard Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('app')->name('dashboard')->group(function () {

    // Dashboard home
    Route::get('/', [DashboardController::class, 'index'])->name('');

    // Settings (form handled by Livewire SettingsForm component)
    Route::get('/settings', [SettingsController::class, 'index'])->name('.settings.index');

    // Audit Log
    Route::get('/audit-log', [AuditLogController::class, 'index'])->name('.audit-log');

    // System Health (Developer only)
    Route::get('/system-health', [SystemHealthController::class, 'index'])->name('.system-health');

    // AI Tools placeholder
    Route::get('/ai-tools', function () {
        abort_unless(auth()->user()->can('ai_tools.view'), 403);

        return view('dashboard.ai-tools.index');
    })->name('.ai-tools');

    // Guided Tours
    Route::get('/tours', function () {
        return view('dashboard.tours.index');
    })->name('.tours');

    // Quotes
    Route::prefix('quotes')->name('.quotes.')->group(function () {
        Route::get('/', [QuoteController::class, 'index'])->name('index');
        Route::get('/create', [QuoteController::class, 'create'])->name('create');
        Route::get('/{quote}', [QuoteController::class, 'show'])->name('show');
    });

    /*
    |----------------------------------------------------------------------
    | Phase 2 stubs — routes added here as modules are built
    |----------------------------------------------------------------------
    | Route::prefix('clients')->name('.clients.')-> ...
    | Route::prefix('jobs')->name('.jobs.')-> ...
    | Route::prefix('invoices')->name('.invoices.')-> ...
    | Route::prefix('payments')->name('.payments.')-> ...
    | Route::prefix('equipment')->name('.equipment.')-> ...
    | Route::prefix('users')->name('.users.')-> ...
    | Route::prefix('roles')->name('.roles.')-> ...
    */
});

/*
|--------------------------------------------------------------------------
| WiPay Payment Callback
| Must be outside auth middleware — WiPay POSTs here after checkout
|--------------------------------------------------------------------------
*/
Route::post('/payments/callback', function (Request $request) {
    $wipay = app(WiPayService::class);
    $result = $wipay->handleCallback($request->all());

    Log::info('WiPay payment callback', $result);

    // TODO Phase 2: Update invoice/payment record based on $result
    // Human must confirm before marking invoice paid (do not auto-approve).

    return response()->json(['received' => true]);
})->name('payments.callback');
