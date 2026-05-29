<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind WiPay service
        $this->app->singleton(\App\Services\WiPay\WiPayService::class);

        // Bind AI service
        $this->app->singleton(\App\Services\AI\AiService::class);
    }

    public function boot(): void
    {
        // Strict mode in non-production
        Model::shouldBeStrict(! app()->isProduction());

        // Developers bypass all gates/policies
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('Developer')) {
                return true;
            }
        });

        // Share company settings with all views
        View::composer('*', function ($view) {
            try {
                $view->with('companyName',   Setting::get('company_name', 'Constructive Cleaning Company LTD'));
                $view->with('companySlogan', Setting::get('company_slogan', 'Efficiency, Constructiveness & Unity — As Far As The Eyes Can See'));
                $view->with('companyMotto',  Setting::get('company_motto', 'Sufficient, Effective, Success'));
                $view->with('logoPath',      Setting::get('logo_path', ''));
            } catch (\Throwable) {
                // Settings table may not exist during install
                $view->with('companyName',   'Constructive Cleaning Company LTD');
                $view->with('companySlogan', 'Efficiency, Constructiveness & Unity — As Far As The Eyes Can See');
                $view->with('companyMotto',  'Sufficient, Effective, Success');
                $view->with('logoPath',      '');
            }
        });

        // Custom Blade directives
        Blade::directive('role', fn ($role) => "<?php if(auth()->check() && auth()->user()->hasRole({$role})): ?>");
        Blade::directive('endrole', fn () => '<?php endif; ?>');

        Blade::directive('permission', fn ($perm) => "<?php if(auth()->check() && auth()->user()->can({$perm})): ?>");
        Blade::directive('endpermission', fn () => '<?php endif; ?>');
    }
}
