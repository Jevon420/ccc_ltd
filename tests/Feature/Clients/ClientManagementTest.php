<?php

namespace Tests\Feature\Clients;

use App\Livewire\Clients\ClientForm;
use App\Livewire\Clients\ClientList;
use App\Models\Client;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ClientManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $director;

    protected User $fieldWorker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->director = User::factory()->create();
        $this->director->assignRole('Director');

        $this->fieldWorker = User::factory()->create();
        $this->fieldWorker->assignRole('Field Worker');
    }

    public function test_clients_index_loads_for_authorised_user(): void
    {
        $this->actingAs($this->director)
            ->get(route('dashboard.clients.index'))
            ->assertStatus(200)
            ->assertSeeLivewire(ClientList::class);
    }

    public function test_clients_index_forbidden_for_field_worker(): void
    {
        $this->actingAs($this->fieldWorker)
            ->get(route('dashboard.clients.index'))
            ->assertStatus(403);
    }

    public function test_client_list_shows_clients(): void
    {
        Client::factory()->count(3)->create();

        Livewire::actingAs($this->director)
            ->test(ClientList::class)
            ->assertSee(Client::first()->name);
    }

    public function test_search_filters_by_name(): void
    {
        Client::factory()->create(['name' => 'Unique Corp XYZ']);
        Client::factory()->create(['name' => 'Someone Else Ltd']);

        Livewire::actingAs($this->director)
            ->test(ClientList::class)
            ->set('search', 'Unique Corp XYZ')
            ->assertSee('Unique Corp XYZ')
            ->assertDontSee('Someone Else Ltd');
    }

    public function test_type_filter_shows_only_companies(): void
    {
        Client::factory()->company()->create(['name' => 'Big Company Ltd']);
        Client::factory()->individual()->create(['name' => 'Jane Doe']);

        Livewire::actingAs($this->director)
            ->test(ClientList::class)
            ->set('typeFilter', 'company')
            ->assertSee('Big Company Ltd')
            ->assertDontSee('Jane Doe');
    }

    public function test_create_company_client(): void
    {
        Livewire::actingAs($this->director)
            ->test(ClientForm::class)
            ->call('openCreate')
            ->set('type', 'company')
            ->set('name', 'ABC Corporation Ltd')
            ->set('contactPerson', 'John Smith')
            ->set('email', 'john@abc.com')
            ->set('phone', '+1 868 555-0000')
            ->set('city', 'Port of Spain')
            ->call('save')
            ->assertSet('isOpen', false);

        $this->assertDatabaseHas('clients', [
            'name' => 'ABC Corporation Ltd',
            'type' => 'company',
            'contact_person' => 'John Smith',
            'is_active' => true,
        ]);
    }

    public function test_create_individual_client(): void
    {
        Livewire::actingAs($this->director)
            ->test(ClientForm::class)
            ->call('openCreate')
            ->set('type', 'individual')
            ->set('name', 'Maria Garcia')
            ->set('email', 'maria@email.com')
            ->call('save')
            ->assertSet('isOpen', false);

        $this->assertDatabaseHas('clients', [
            'name' => 'Maria Garcia',
            'type' => 'individual',
        ]);
    }

    public function test_edit_client(): void
    {
        $client = Client::factory()->company()->create(['name' => 'Old Name Ltd']);

        Livewire::actingAs($this->director)
            ->test(ClientForm::class)
            ->call('openEdit', $client->id)
            ->set('name', 'New Name Ltd')
            ->call('save')
            ->assertSet('isOpen', false);

        $this->assertDatabaseHas('clients', ['id' => $client->id, 'name' => 'New Name Ltd']);
    }

    public function test_archive_client(): void
    {
        $client = Client::factory()->create();

        Livewire::actingAs($this->director)
            ->test(ClientList::class)
            ->call('confirmDelete', $client->id)
            ->call('deleteClient');

        $this->assertSoftDeleted('clients', ['id' => $client->id]);
    }

    public function test_restore_archived_client(): void
    {
        $client = Client::factory()->create();
        $client->delete();

        Livewire::actingAs($this->director)
            ->test(ClientList::class)
            ->set('showTrashed', true)
            ->call('confirmRestore', $client->id)
            ->call('restoreClient');

        $this->assertNotSoftDeleted('clients', ['id' => $client->id]);
    }

    public function test_field_worker_cannot_create_clients(): void
    {
        Livewire::actingAs($this->fieldWorker)
            ->test(ClientForm::class)
            ->call('openCreate')
            ->assertForbidden();
    }

    public function test_name_is_required(): void
    {
        Livewire::actingAs($this->director)
            ->test(ClientForm::class)
            ->call('openCreate')
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['name']);
    }
}
