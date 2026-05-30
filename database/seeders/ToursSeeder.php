<?php

namespace Database\Seeders;

use App\Models\Tour;
use App\Models\TourStep;
use Illuminate\Database\Seeder;

class ToursSeeder extends Seeder
{
    public function run(): void
    {
        // Dashboard Introduction Tour
        $dashboardTour = Tour::updateOrCreate(
            ['name' => 'dashboard_intro'],
            [
                'title' => 'Welcome to CCC Ops',
                'description' => 'A quick introduction to your dashboard',
                'route_name' => 'dashboard',
                'role_scope' => null, // applies to all roles
                'is_active' => true,
                'is_required' => false,
                'sort_order' => 1,
            ]
        );

        $dashboardSteps = [
            [
                'title' => 'Welcome to CCC Ops! 👋',
                'description' => 'This is your operations dashboard for Constructive Cleaning Company LTD. Let us show you around.',
                'element' => null,
                'placement' => 'center',
                'sort_order' => 1,
                'is_required' => false,
            ],
            [
                'title' => 'Your Dashboard Overview',
                'description' => 'These cards give you a live snapshot of your operations — pending jobs, quotes, invoices, and more.',
                'element' => '#dashboard-cards',
                'placement' => 'bottom',
                'sort_order' => 2,
                'is_required' => false,
            ],
            [
                'title' => 'Navigation Sidebar',
                'description' => 'Use the sidebar to navigate between all modules. You will only see what your role permits.',
                'element' => '#app-sidebar',
                'placement' => 'right',
                'sort_order' => 3,
                'is_required' => false,
            ],
            [
                'title' => 'Recent Activity',
                'description' => 'This panel shows recent system activity and logs so you can always see what is happening.',
                'element' => '#recent-activity',
                'placement' => 'left',
                'sort_order' => 4,
                'is_required' => false,
            ],
            [
                'title' => "You're all set!",
                'description' => 'You can replay this tour anytime from Settings → Guided Tours. Good luck!',
                'element' => null,
                'placement' => 'center',
                'sort_order' => 5,
                'is_required' => false,
            ],
        ];

        foreach ($dashboardSteps as $step) {
            TourStep::updateOrCreate(
                ['tour_id' => $dashboardTour->id, 'sort_order' => $step['sort_order']],
                array_merge($step, ['tour_id' => $dashboardTour->id])
            );
        }

        // Settings Tour
        $settingsTour = Tour::updateOrCreate(
            ['name' => 'settings_overview'],
            [
                'title' => 'Company Settings',
                'description' => 'Learn how to configure your system',
                'route_name' => 'dashboard.settings.index',
                'role_scope' => ['Developer', 'Director', 'Admin Officer'],
                'is_active' => true,
                'is_required' => false,
                'sort_order' => 2,
            ]
        );

        $settingsSteps = [
            [
                'title' => 'Company Settings',
                'description' => 'This is where you configure your company information, feature flags, and system options.',
                'element' => null,
                'placement' => 'center',
                'sort_order' => 1,
                'is_required' => false,
            ],
            [
                'title' => 'Company Information',
                'description' => 'Update your company name, contact details, slogan, and logo here.',
                'element' => '#settings-company',
                'placement' => 'bottom',
                'sort_order' => 2,
                'is_required' => false,
            ],
            [
                'title' => 'Feature Flags',
                'description' => 'Toggle features like AI tools, public site, and quote requests on or off.',
                'element' => '#settings-features',
                'placement' => 'top',
                'sort_order' => 3,
                'is_required' => false,
            ],
        ];

        foreach ($settingsSteps as $step) {
            TourStep::updateOrCreate(
                ['tour_id' => $settingsTour->id, 'sort_order' => $step['sort_order']],
                array_merge($step, ['tour_id' => $settingsTour->id])
            );
        }

        $this->command->info('Guided tours seeded (dashboard_intro, settings_overview).');
    }
}
