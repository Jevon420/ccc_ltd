<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the default Developer and Admin users.
     *
     * These users are safe for both local development AND production first-run.
     *
     * ⚠️  CHANGE THE PASSWORDS BELOW before running on production!
     *    Or use environment variables: DEVELOPER_PASSWORD, ADMIN_PASSWORD
     */
    public function run(): void
    {
        // -----------------------------------------------------------------
        // Developer User (full system access)
        // -----------------------------------------------------------------
        $developer = User::updateOrCreate(
            ['email' => env('DEVELOPER_EMAIL', 'developer@ccc-ops.local')],
            [
                'name'              => 'System Developer',
                'email'             => env('DEVELOPER_EMAIL', 'developer@ccc-ops.local'),
                'password'          => Hash::make(env('DEVELOPER_PASSWORD', 'Dev@CCC#2025!')),
                'email_verified_at' => now(),
                'position'          => 'System Developer',
                'is_active'         => true,
            ]
        );

        $developer->syncRoles(['Developer']);

        $this->command->info("Developer user: {$developer->email}");

        // -----------------------------------------------------------------
        // Admin Officer User (day-to-day admin access)
        // -----------------------------------------------------------------
        $admin = User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@constructivecleaningco.com')],
            [
                'name'              => 'Admin Officer',
                'email'             => env('ADMIN_EMAIL', 'admin@constructivecleaningco.com'),
                'password'          => Hash::make(env('ADMIN_PASSWORD', 'Admin@CCC#2025!')),
                'email_verified_at' => now(),
                'position'          => 'Admin Officer',
                'is_active'         => true,
            ]
        );

        $admin->syncRoles(['Admin Officer']);

        $this->command->info("Admin user: {$admin->email}");

        $this->command->warn('⚠  Remember to change default passwords via .env before deploying to production!');
        $this->command->line('   DEVELOPER_PASSWORD=<your-password>');
        $this->command->line('   ADMIN_PASSWORD=<your-password>');
    }
}
