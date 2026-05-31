<?php

namespace Tests\Feature\Equipment;

use App\Livewire\Equipment\EquipmentList;
use App\Models\Equipment;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EquipmentTest extends TestCase
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

    public function test_equipment_page_loads(): void
    {
        $this->actingAs($this->director)
            ->get(route('dashboard.equipment.index'))
            ->assertStatus(200)
            ->assertSeeLivewire(EquipmentList::class);
    }

    public function test_driver_can_view_equipment(): void
    {
        $this->actingAs($this->driver)
            ->get(route('dashboard.equipment.index'))
            ->assertStatus(200);
    }

    public function test_create_equipment(): void
    {
        Livewire::actingAs($this->director)
            ->test(EquipmentList::class)
            ->call('openCreate')
            ->set('name', 'Karcher Pressure Washer')
            ->set('type', 'machinery')
            ->set('serialNumber', 'SN-12345678')
            ->set('condition', 'excellent')
            ->set('status', 'active')
            ->call('save')
            ->assertSet('isOpen', false);

        $this->assertDatabaseHas('equipment', [
            'name' => 'Karcher Pressure Washer',
            'type' => 'machinery',
            'serial_number' => 'SN-12345678',
            'status' => 'active',
        ]);
    }

    public function test_edit_equipment(): void
    {
        $item = Equipment::factory()->create(['name' => 'Old Name', 'status' => 'active']);

        Livewire::actingAs($this->director)
            ->test(EquipmentList::class)
            ->call('openEdit', $item->id)
            ->set('name', 'Updated Name')
            ->set('status', 'maintenance')
            ->call('save');

        $this->assertDatabaseHas('equipment', ['id' => $item->id, 'name' => 'Updated Name', 'status' => 'maintenance']);
    }

    public function test_archive_and_restore_equipment(): void
    {
        $item = Equipment::factory()->create();

        Livewire::actingAs($this->director)
            ->test(EquipmentList::class)
            ->call('confirmDelete', $item->id)
            ->call('deleteEquipment');

        $this->assertSoftDeleted('equipment', ['id' => $item->id]);

        Livewire::actingAs($this->director)
            ->test(EquipmentList::class)
            ->set('showTrashed', true)
            ->call('confirmRestore', $item->id)
            ->call('restoreEquipment');

        $this->assertNotSoftDeleted('equipment', ['id' => $item->id]);
    }

    public function test_driver_cannot_create_equipment(): void
    {
        Livewire::actingAs($this->driver)
            ->test(EquipmentList::class)
            ->call('openCreate')
            ->assertForbidden();
    }

    public function test_status_colour_helpers(): void
    {
        $item = Equipment::factory()->create(['status' => 'maintenance', 'condition' => 'fair']);
        $this->assertStringContainsString('amber', $item->statusColour);
        $this->assertStringContainsString('amber', $item->conditionColour);
    }
}
