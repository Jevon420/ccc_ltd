<?php

namespace Tests\Feature\Quotes;

use App\Ai\Agents\QuoteDrafter as QuoteDrafterAgent;
use App\Jobs\GenerateQuoteDraft;
use App\Livewire\Quotes\QuoteDrafter;
use App\Models\Quote;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
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
        $this->actingAs($this->developer)
            ->get(route('dashboard.quotes.create'))
            ->assertStatus(200)
            ->assertSeeLivewire(QuoteDrafter::class);
    }

    public function test_quote_create_page_forbidden_for_guest(): void
    {
        $this->get(route('dashboard.quotes.create'))->assertRedirect(route('login'));
    }

    public function test_generate_validates_required_fields(): void
    {
        Queue::fake();

        Livewire::actingAs($this->developer)
            ->test(QuoteDrafter::class)
            ->call('generate')
            ->assertHasErrors(['client_name', 'service_type', 'job_details']);

        Queue::assertNothingPushed();
    }

    public function test_generate_saves_draft_quote_and_dispatches_job(): void
    {
        Queue::fake();

        Livewire::actingAs($this->developer)
            ->test(QuoteDrafter::class)
            ->set('client_name', 'ABC Ltd')
            ->set('service_type', 'Deep Cleaning')
            ->set('location', 'Port of Spain')
            ->set('job_details', 'Full deep clean of 3-storey commercial building including all restrooms and common areas.')
            ->call('generate')
            ->assertSet('generating', true)
            ->assertHasNoErrors();

        // A draft quote should be saved immediately
        $this->assertDatabaseHas('quotes', [
            'client_name' => 'ABC Ltd',
            'service_type' => 'Deep Cleaning',
            'status' => 'draft',
        ]);

        // The AI job should be dispatched to the queue
        Queue::assertPushed(GenerateQuoteDraft::class, function ($job) {
            return str_contains($job->prompt, 'ABC Ltd');
        });
    }

    public function test_generate_job_writes_ai_draft_to_quote(): void
    {
        QuoteDrafterAgent::fake(['CCC will provide a thorough deep clean of the premises.']);

        $quote = Quote::create([
            'client_name' => 'ABC Ltd',
            'service_type' => 'Deep Cleaning',
            'job_details' => 'Full deep clean',
            'status' => 'draft',
        ]);

        // Run the job directly (simulates queue worker processing it)
        (new GenerateQuoteDraft($quote->id, 'Client: ABC Ltd'))->handle();

        $this->assertDatabaseHas('quotes', [
            'id' => $quote->id,
            'ai_draft' => 'CCC will provide a thorough deep clean of the premises.',
        ]);
    }

    public function test_check_draft_detects_completed_ai_draft(): void
    {
        $quote = Quote::create([
            'client_name' => 'Test Client',
            'service_type' => 'Commercial Cleaning',
            'job_details' => 'Office clean weekly',
            'ai_draft' => 'The AI has generated this draft.',
            'status' => 'draft',
        ]);

        Livewire::actingAs($this->developer)
            ->test(QuoteDrafter::class, ['generating' => true, 'draftQuoteId' => $quote->id])
            ->call('checkDraft')
            ->assertSet('saved', true)
            ->assertSet('generating', false);
    }
}
