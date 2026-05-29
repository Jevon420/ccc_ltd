<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_loads(): void
    {
        $this->get(route('login'))->assertStatus(200);
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        Role::firstOrCreate(['name' => 'Developer', 'guard_name' => 'web']);

        $user = User::factory()->create([
            'password'  => bcrypt('password123'),
            'is_active' => true,
        ]);
        $user->assignRole('Developer');

        $this->post(route('login.post'), [
            'email'    => $user->email,
            'password' => 'password123',
        ])->assertRedirect(route('dashboard'));
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        $user = User::factory()->create(['password' => bcrypt('correct-password')]);

        $this->post(route('login.post'), [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');
    }

    public function test_inactive_user_cannot_login(): void
    {
        Role::firstOrCreate(['name' => 'Field Worker', 'guard_name' => 'web']);

        $user = User::factory()->create([
            'password'  => bcrypt('password123'),
            'is_active' => false,
        ]);
        $user->assignRole('Field Worker');

        $this->post(route('login.post'), [
            'email'    => $user->email,
            'password' => 'password123',
        ])->assertSessionHasErrors('email');
    }

    public function test_user_can_logout(): void
    {
        Role::firstOrCreate(['name' => 'Developer', 'guard_name' => 'web']);

        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('Developer');

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('home'));
    }
}
