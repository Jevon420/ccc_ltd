<?php

namespace Tests\Feature\Users;

use App\Livewire\Users\UserForm;
use App\Livewire\Users\UserList;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $developer;

    protected User $supervisor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->developer = User::factory()->create();
        $this->developer->assignRole('Developer');

        $this->supervisor = User::factory()->create();
        $this->supervisor->assignRole('Supervisor');
    }

    public function test_users_index_loads_for_authorised_user(): void
    {
        $this->actingAs($this->developer)
            ->get(route('dashboard.users.index'))
            ->assertStatus(200)
            ->assertSeeLivewire(UserList::class);
    }

    public function test_users_index_forbidden_for_unauthorised_role(): void
    {
        $this->actingAs($this->supervisor)
            ->get(route('dashboard.users.index'))
            ->assertStatus(403);
    }

    public function test_user_list_shows_users(): void
    {
        User::factory()->count(3)->create();

        Livewire::actingAs($this->developer)
            ->test(UserList::class)
            ->assertSee($this->developer->name);
    }

    public function test_user_list_search_filters_results(): void
    {
        $target = User::factory()->create(['name' => 'Unique Person XYZ']);
        User::factory()->create(['name' => 'Someone Else']);

        Livewire::actingAs($this->developer)
            ->test(UserList::class)
            ->set('search', 'Unique Person XYZ')
            ->assertSee('Unique Person XYZ')
            ->assertDontSee('Someone Else');
    }

    public function test_create_user_via_form(): void
    {
        Livewire::actingAs($this->developer)
            ->test(UserForm::class)
            ->call('openCreate')
            ->set('name', 'New Staff Member')
            ->set('email', 'newstaff@ccc.com')
            ->set('role', 'Field Worker')
            ->set('password', 'password123')
            ->call('save')
            ->assertSet('isOpen', false);

        $this->assertDatabaseHas('users', [
            'name' => 'New Staff Member',
            'email' => 'newstaff@ccc.com',
        ]);
    }

    public function test_edit_user_via_form(): void
    {
        $user = User::factory()->create(['name' => 'Old Name']);
        $user->assignRole('Field Worker');

        Livewire::actingAs($this->developer)
            ->test(UserForm::class)
            ->call('openEdit', $user->id)
            ->set('name', 'Updated Name')
            ->call('save')
            ->assertSet('isOpen', false);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);
    }

    public function test_deactivate_user(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Field Worker');

        Livewire::actingAs($this->developer)
            ->test(UserList::class)
            ->call('confirmDelete', $user->id)
            ->call('deleteUser');

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_cannot_deactivate_own_account(): void
    {
        Livewire::actingAs($this->developer)
            ->test(UserList::class)
            ->call('confirmDelete', $this->developer->id)
            ->call('deleteUser')
            ->assertDispatched('toast');

        $this->assertNull(User::find($this->developer->id)->deleted_at);
    }

    public function test_restore_deactivated_user(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Field Worker');
        $user->delete();

        Livewire::actingAs($this->developer)
            ->test(UserList::class)
            ->set('showTrashed', true)
            ->call('confirmRestore', $user->id)
            ->call('restoreUser');

        $this->assertNotSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_supervisor_cannot_create_users(): void
    {
        Livewire::actingAs($this->supervisor)
            ->test(UserForm::class)
            ->call('openCreate')
            ->assertForbidden();
    }
}
