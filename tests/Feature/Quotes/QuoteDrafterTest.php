<?php

namespace Tests\Feature\Quotes;

use App\Ai\Agents\QuoteDrafter as QuoteDrafterAgent;
use App\Livewire\Quotes\QuoteDrafter;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class QuoteDrafterTest extends TestCase
{
    use RefreshDatabase;

    protected User $developer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->developer = User::factory()->create();
        $this->developer->assignRole('Developer');
    }

    public function test_quote_create_page_loads_for_authorised_user(): void
    {
        $response = $this->actingAs($this->developer)->get(route('dashboard.quotes.create'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(QuoteDrafter::class);
    }

    public function test_quote_create_page_forbidden_for_guest(): void
    {
        $this->get(route('dashboard.quotes.create'))->assertRedirect(route('login'));
    }

    public function test_generate_validates_required_fields(): void
    {
        QuoteDrafterAgent::fake(['A professional cleaning quote.']);

        Livewire::actingAs($this->developer)
            ->test(QuoteDrafter::class)
            ->call('generate')
            ->assertHasErrors(['client_name', 'service_type', 'job_details']);
    }

    public function test_generate_calls_ai_agent_and_sets_draft(): void
    {
        QuoteDrafterAgent::fake(['CCC will provide a thorough deep clean of the premises.']);

        $component = Livewire::actingAs($this->developer)
            ->test(QuoteDrafter::class)
            ->set('client_name', 'ABC Ltd')
            ->set('service_type', 'Deep Cleaning')
            ->set('location', 'Port of Spain')
            ->set('job_details', 'Full deep clean of 3-storey commercial building including all restrooms and common areas.')
            ->call('generate');

        $this->assertNull($component->get('errorMessage'), 'AI error: '.$component->get('errorMessage'));
        $component->assertSet('ai_draft', 'CCC will provide a thorough deep clean of the premises.')
            ->assertNoRedirect();
    }

    public function test_save_quote_persists_to_database(): void
    {
        QuoteDrafterAgent::fake(['Professional quote draft.']);

        Livewire::actingAs($this->developer)
            ->test(QuoteDrafter::class)
            ->set('client_name', 'XYZ Corp')
            ->set('client_email', 'contact@xyz.com')
            ->set('service_type', 'Commercial Cleaning')
            ->set('location', 'San Fernando')
            ->set('job_details', 'Weekly janitorial service for a 10,000 sq ft office complex.')
            ->set('ai_draft', 'Professional quote draft.')
            ->call('saveQuote')
            ->assertSet('saved', true);

        $this->assertDatabaseHas('quotes', [
            'client_name' => 'XYZ Corp',
            'client_email' => 'contact@xyz.com',
            'service_type' => 'Commercial Cleaning',
            'status' => 'draft',
        ]);
    }

    public function test_save_quote_resets_form_after_save(): void
    {
        QuoteDrafterAgent::fake(['Draft.']);

        Livewire::actingAs($this->developer)
            ->test(QuoteDrafter::class)
            ->set('client_name', 'Test Client')
            ->set('service_type', 'Residential Cleaning')
            ->set('job_details', 'Clean a 4-bedroom house top to bottom including all bathrooms.')
            ->set('ai_draft', 'Draft.')
            ->call('saveQuote')
            ->assertSet('client_name', '')
            ->assertSet('ai_draft', '');
    }
}
