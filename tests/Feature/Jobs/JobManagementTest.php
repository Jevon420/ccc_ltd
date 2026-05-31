<?php

namespace Tests\Feature\Jobs;

use App\Livewire\Jobs\JobForm;
use App\Livewire\Jobs\JobList;
use App\Livewire\Jobs\JobShow;
use App\Models\Client;
use App\Models\Job;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class JobManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $manager;

    protected User $fieldWorker;

    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->manager = User::factory()->create();
        $this->manager->assignRole('Operations Manager');

        $this->fieldWorker = User::factory()->create();
        $this->fieldWorker->assignRole('Field Worker');

        $this->client = Client::factory()->create();
    }

    public function test_jobs_index_loads_for_authorised_user(): void
    {
        $this->actingAs($this->manager)
            ->get(route('dashboard.jobs.index'))
            ->assertStatus(200)
            ->assertSeeLivewire(JobList::class);
    }

    public function test_jobs_list_shows_jobs(): void
    {
        Job::factory()->count(3)->create(['client_id' => $this->client->id]);

        Livewire::actingAs($this->manager)
            ->test(JobList::class)
            ->assertSee('JOB-');
    }

    public function test_search_filters_by_reference(): void
    {
        $job = Job::factory()->create(['client_id' => $this->client->id, 'title' => 'Unique Search Title XYZ']);

        Livewire::actingAs($this->manager)
            ->test(JobList::class)
            ->set('search', $job->reference)
            ->assertSee($job->reference);
    }

    public function test_status_filter_works(): void
    {
        Job::factory()->pending()->create(['client_id' => $this->client->id]);
        Job::factory()->completed()->create(['client_id' => $this->client->id]);

        Livewire::actingAs($this->manager)
            ->test(JobList::class)
            ->set('statusFilter', 'pending')
            ->assertSee('Pending');
    }

    public function test_create_job(): void
    {
        Livewire::actingAs($this->manager)
            ->test(JobForm::class)
            ->call('openCreate')
            ->set('title', 'Office Clean — ABC Corp')
            ->set('clientId', $this->client->id)
            ->set('status', 'pending')
            ->set('priority', 'high')
            ->set('scheduledDate', '2026-06-15')
            ->call('save')
            ->assertSet('isOpen', false);

        $this->assertDatabaseHas('work_jobs', [
            'title' => 'Office Clean — ABC Corp',
            'client_id' => $this->client->id,
            'priority' => 'high',
        ]);
    }

    public function test_job_gets_auto_reference(): void
    {
        Livewire::actingAs($this->manager)
            ->test(JobForm::class)
            ->call('openCreate')
            ->set('title', 'Test Job')
            ->set('clientId', $this->client->id)
            ->call('save');

        $this->assertDatabaseHas('work_jobs', ['title' => 'Test Job']);
        $job = Job::where('title', 'Test Job')->first();
        $this->assertStringStartsWith('JOB-', $job->reference);
    }

    public function test_edit_job(): void
    {
        $job = Job::factory()->create(['client_id' => $this->client->id]);

        Livewire::actingAs($this->manager)
            ->test(JobForm::class)
            ->call('openEdit', $job->id)
            ->set('title', 'Updated Title')
            ->set('status', 'in_progress')
            ->call('save')
            ->assertSet('isOpen', false);

        $this->assertDatabaseHas('work_jobs', ['id' => $job->id, 'title' => 'Updated Title', 'status' => 'in_progress']);
    }

    public function test_job_show_page_loads(): void
    {
        $job = Job::factory()->create(['client_id' => $this->client->id]);

        $this->actingAs($this->manager)
            ->get(route('dashboard.jobs.show', $job))
            ->assertStatus(200)
            ->assertSeeLivewire(JobShow::class);
    }

    public function test_update_status_from_show_page(): void
    {
        $job = Job::factory()->pending()->create(['client_id' => $this->client->id]);

        Livewire::actingAs($this->manager)
            ->test(JobShow::class, ['job' => $job])
            ->call('updateStatus', 'in_progress');

        $this->assertDatabaseHas('work_jobs', ['id' => $job->id, 'status' => 'in_progress']);
    }

    public function test_cancel_job(): void
    {
        $job = Job::factory()->create(['client_id' => $this->client->id]);

        Livewire::actingAs($this->manager)
            ->test(JobList::class)
            ->call('confirmDelete', $job->id)
            ->call('deleteJob');

        $this->assertSoftDeleted('work_jobs', ['id' => $job->id]);
    }

    public function test_field_worker_can_view_but_not_create_jobs(): void
    {
        // Field Workers can view jobs
        $this->actingAs($this->fieldWorker)
            ->get(route('dashboard.jobs.index'))
            ->assertStatus(200);

        // But cannot create
        Livewire::actingAs($this->fieldWorker)
            ->test(JobForm::class)
            ->call('openCreate')
            ->assertForbidden();
    }

    public function test_client_is_required(): void
    {
        Livewire::actingAs($this->manager)
            ->test(JobForm::class)
            ->call('openCreate')
            ->set('title', 'Job without client')
            ->call('save')
            ->assertHasErrors(['clientId']);
    }
}
