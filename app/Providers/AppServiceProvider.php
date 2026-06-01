<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\User;
use App\Services\AI\AiService;
use App\Services\WiPay\WiPayService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Pulse\Facades\Pulse;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind WiPay service
        $this->app->singleton(WiPayService::class);

        // Bind AI service
        $this->app->singleton(AiService::class);
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

        // Gate Pulse to Developer & Director roles only
        Gate::define('viewPulse', function (User $user) {
            return $user->hasRole('Developer') || $user->hasRole('Director');
        });

        // Pulse user resolver — show names and roles instead of emails
        // Note: $ids may be a Collection or array depending on Pulse version
        Pulse::users(function (iterable $ids) {
            return User::findMany(collect($ids)->toArray())
                ->map(fn (User $user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'extra' => $user->getRoleNames()->first() ?? '',
                    'avatar' => $user->avatar_url,
                ]);
        });

        // Share company settings with all views
        View::composer('*', function ($view) {
            try {
                $view->with('companyName', Setting::get('company_name', 'Constructive Cleaning Company LTD'));
                $view->with('companySlogan', Setting::get('company_slogan', 'Efficiency, Constructiveness & Unity — As Far As The Eyes Can See'));
                $view->with('companyMotto', Setting::get('company_motto', 'Sufficient, Effective, Success'));
                $view->with('logoPath', Setting::get('logo_path', ''));
            } catch (\Throwable) {
                // Settings table may not exist during install
                $view->with('companyName', 'Constructive Cleaning Company LTD');
                $view->with('companySlogan', 'Efficiency, Constructiveness & Unity — As Far As The Eyes Can See');
                $view->with('companyMotto', 'Sufficient, Effective, Success');
                $view->with('logoPath', '');
            }
        });

        // Custom Blade directives
        Blade::directive('role', fn ($role) => "<?php if(auth()->check() && auth()->user()->hasRole({$role})): ?>");
        Blade::directive('endrole', fn () => '<?php endif; ?>');

        Blade::directive('permission', fn ($perm) => "<?php if(auth()->check() && auth()->user()->can({$perm})): ?>");
        Blade::directive('endpermission', fn () => '<?php endif; ?>');
    }
}
