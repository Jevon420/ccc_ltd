<?php

namespace Tests\Feature\Financial;

use App\Livewire\Quotes\QuoteList;
use App\Livewire\Quotes\QuoteShow;
use App\Models\Client;
use App\Models\Quote;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class QuoteWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminOfficer;

    protected User $driver;

    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->adminOfficer = User::factory()->create();
        $this->adminOfficer->assignRole('Admin Officer');

        $this->driver = User::factory()->create();
        $this->driver->assignRole('Driver');

        $this->client = Client::factory()->create();
    }

    public function test_quotes_index_loads(): void
    {
        $this->actingAs($this->adminOfficer)
            ->get(route('dashboard.quotes.index'))
            ->assertStatus(200)
            ->assertSeeLivewire(QuoteList::class);
    }

    public function test_quote_gets_auto_reference(): void
    {
        $quote = Quote::create([
            'client_id' => $this->client->id,
            'client_name' => $this->client->name,
            'title' => 'Test Quote',
            'service_type' => 'Commercial Cleaning',
            'job_details' => 'Clean the office',
            'status' => 'draft',
        ]);

        $this->assertStringStartsWith('QUO-', $quote->reference);
    }

    public function test_quote_status_colour_helper(): void
    {
        $quote = Quote::factory()->create(['status' => 'accepted', 'client_name' => 'Test', 'service_type' => 'Deep Cleaning', 'job_details' => 'Full clean']);
        $this->assertStringContainsString('green', $quote->statusColour);
    }

    public function test_quote_show_page_loads(): void
    {
        $quote = Quote::create([
            'client_id' => $this->client->id,
            'client_name' => $this->client->name,
            'title' => 'Office Clean',
            'service_type' => 'Commercial Cleaning',
            'job_details' => 'Deep clean of all floors',
            'status' => 'draft',
        ]);

        $this->actingAs($this->adminOfficer)
            ->get(route('dashboard.quotes.show', $quote))
            ->assertStatus(200)
            ->assertSeeLivewire(QuoteShow::class);
    }

    public function test_mark_sent_from_show(): void
    {
        $quote = Quote::create([
            'client_id' => $this->client->id,
            'client_name' => $this->client->name,
            'title' => 'Test',
            'service_type' => 'Deep Cleaning',
            'job_details' => 'Full deep clean',
            'status' => 'draft',
        ]);

        Livewire::actingAs($this->adminOfficer)
            ->test(QuoteShow::class, ['quote' => $quote])
            ->call('markSent');

        $this->assertDatabaseHas('quotes', ['id' => $quote->id, 'status' => 'sent']);
    }

    public function test_approve_quote(): void
    {
        $quote = Quote::create([
            'client_id' => $this->client->id,
            'client_name' => $this->client->name,
            'title' => 'Test',
            'service_type' => 'Deep Cleaning',
            'job_details' => 'Full deep clean',
            'status' => 'sent',
        ]);

        Livewire::actingAs($this->adminOfficer)
            ->test(QuoteShow::class, ['quote' => $quote])
            ->call('approve');

        $this->assertDatabaseHas('quotes', ['id' => $quote->id, 'status' => 'accepted']);
    }

    public function test_decline_quote(): void
    {
        $quote = Quote::create([
            'client_id' => $this->client->id,
            'client_name' => $this->client->name,
            'title' => 'Test',
            'service_type' => 'Deep Cleaning',
            'job_details' => 'Full deep clean',
            'status' => 'sent',
        ]);

        Livewire::actingAs($this->adminOfficer)
            ->test(QuoteShow::class, ['quote' => $quote])
            ->call('decline');

        $this->assertDatabaseHas('quotes', ['id' => $quote->id, 'status' => 'declined']);
    }

    public function test_driver_cannot_view_quotes(): void
    {
        $this->actingAs($this->driver)
            ->get(route('dashboard.quotes.index'))
            ->assertStatus(403);
    }
}
