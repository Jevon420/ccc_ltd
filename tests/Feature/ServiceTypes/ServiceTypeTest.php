<?php

namespace Tests\Feature\ServiceTypes;

use App\Livewire\ServiceTypes\ServiceTypeList;
use App\Models\ServiceType;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ServiceTypeTest extends TestCase
{
    use RefreshDatabase;

    protected User $director;

    protected User $driver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->director = User::factory()->create();
        $this->director->assignRole('Director');

        $this->driver = User::factory()->create();
        $this->driver->assignRole('Driver');
    }

    public function test_service_types_page_loads(): void
    {
        $this->actingAs($this->director)
            ->get(route('dashboard.service-types.index'))
            ->assertStatus(200)
            ->assertSeeLivewire(ServiceTypeList::class);
    }

    public function test_page_forbidden_for_driver(): void
    {
        $this->actingAs($this->driver)
            ->get(route('dashboard.service-types.index'))
            ->assertStatus(403);
    }

    public function test_create_service_type(): void
    {
        Livewire::actingAs($this->director)
            ->test(ServiceTypeList::class)
            ->set('showCreateForm', true)
            ->set('newName', 'Pressure Washing')
            ->set('newDescription', 'High-pressure water cleaning for driveways and exteriors.')
            ->call('create')
            ->assertSet('showCreateForm', false);

        $this->assertDatabaseHas('service_types', ['name' => 'Pressure Washing']);
    }

    public function test_duplicate_name_is_rejected(): void
    {
        ServiceType::factory()->create(['name' => 'Existing Service']);

        Livewire::actingAs($this->director)
            ->test(ServiceTypeList::class)
            ->set('showCreateForm', true)
            ->set('newName', 'Existing Service')
            ->call('create')
            ->assertHasErrors(['newName']);
    }

    public function test_edit_service_type(): void
    {
        $type = ServiceType::factory()->create(['name' => 'Old Name']);

        Livewire::actingAs($this->director)
            ->test(ServiceTypeList::class)
            ->call('startEdit', $type->id)
            ->set('editName', 'Updated Name')
            ->call('saveEdit');

        $this->assertDatabaseHas('service_types', ['id' => $type->id, 'name' => 'Updated Name']);
    }

    public function test_toggle_active_status(): void
    {
        $type = ServiceType::factory()->create(['is_active' => true]);

        Livewire::actingAs($this->director)
            ->test(ServiceTypeList::class)
            ->call('toggleActive', $type->id);

        $this->assertDatabaseHas('service_types', ['id' => $type->id, 'is_active' => false]);
    }

    public function test_delete_service_type(): void
    {
        $type = ServiceType::factory()->create();

        Livewire::actingAs($this->director)
            ->test(ServiceTypeList::class)
            ->call('confirmDelete', $type->id)
            ->call('deleteServiceType');

        $this->assertDatabaseMissing('service_types', ['id' => $type->id]);
    }

    public function test_driver_cannot_view_service_types_page(): void
    {
        $this->actingAs($this->driver)
            ->get(route('dashboard.service-types.index'))
            ->assertStatus(403);
    }
}
