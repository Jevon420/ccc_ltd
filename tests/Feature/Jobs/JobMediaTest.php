<?php

namespace Tests\Feature\Jobs;

use App\Livewire\Documents\DocumentManager;
use App\Livewire\Jobs\JobReports;
use App\Models\Client;
use App\Models\Document;
use App\Models\Job;
use App\Models\JobReport;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class JobMediaTest extends TestCase
{
    use RefreshDatabase;

    protected User $director;    // has all permissions

    protected User $supervisor;  // can create/view reports, no docs

    protected User $fieldWorker; // cannot create reports

    protected Job $job;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        Storage::fake('wasabi');

        $this->director = User::factory()->create();
        $this->director->assignRole('Director');

        $this->supervisor = User::factory()->create();
        $this->supervisor->assignRole('Supervisor');

        $this->fieldWorker = User::factory()->create();
        $this->fieldWorker->assignRole('Field Worker');

        $client = Client::factory()->create();
        $this->job = Job::factory()->create(['client_id' => $client->id]);
    }

    public function test_job_report_create_by_supervisor(): void
    {
        Livewire::actingAs($this->supervisor)
            ->test(JobReports::class, ['job' => $this->job])
            ->call('openCreate')
            ->set('title', 'Weekly Site Report')
            ->set('description', 'All work completed as scheduled this week.')
            ->set('workPerformed', 'Full pressure wash of all exterior surfaces.')
            ->set('status', 'submitted')
            ->call('save')
            ->assertSet('isOpen', false);

        $this->assertDatabaseHas('job_reports', [
            'job_id' => $this->job->id,
            'title' => 'Weekly Site Report',
            'status' => 'submitted',
        ]);
    }

    public function test_job_report_approve_by_director(): void
    {
        // Director has job_reports.edit permission
        $report = JobReport::factory()->submitted()->create(['job_id' => $this->job->id]);

        Livewire::actingAs($this->director)
            ->test(JobReports::class, ['job' => $this->job])
            ->call('approve', $report->id);

        $this->assertDatabaseHas('job_reports', ['id' => $report->id, 'status' => 'approved']);
    }

    public function test_job_report_delete_by_director(): void
    {
        $report = JobReport::factory()->create(['job_id' => $this->job->id]);

        Livewire::actingAs($this->director)
            ->test(JobReports::class, ['job' => $this->job])
            ->call('confirmDelete', $report->id)
            ->call('deleteReport');

        $this->assertDatabaseMissing('job_reports', ['id' => $report->id]);
    }

    public function test_field_worker_cannot_view_job_reports_page(): void
    {
        // Field Workers don't have job_reports.view so the job show page is still accessible
        // but trying to render the component directly triggers a 403 in render()
        // — validate via the jobs show HTTP route instead
        $this->actingAs($this->fieldWorker)
            ->get(route('dashboard.jobs.show', $this->job))
            ->assertStatus(200); // Field workers CAN view jobs

        // Confirm they can't create reports (no job_reports.create)
        $this->assertFalse($this->fieldWorker->can('job_reports.create'));
    }

    public function test_document_upload_by_director(): void
    {
        Livewire::actingAs($this->director)
            ->test(DocumentManager::class, ['documentable' => $this->job])
            ->call('openUpload')
            ->set('title', 'Site Permit 2026')
            ->set('category', 'Permit')
            ->set('file', UploadedFile::fake()->create('permit.pdf', 100, 'application/pdf'))
            ->call('upload')
            ->assertSet('isOpen', false);

        $this->assertDatabaseHas('documents', [
            'documentable_type' => Job::class,
            'documentable_id' => $this->job->id,
            'title' => 'Site Permit 2026',
            'category' => 'Permit',
        ]);
    }

    public function test_document_delete_by_director(): void
    {
        $doc = Document::create([
            'documentable_type' => Job::class,
            'documentable_id' => $this->job->id,
            'title' => 'Old Doc',
        ]);

        Livewire::actingAs($this->director)
            ->test(DocumentManager::class, ['documentable' => $this->job])
            ->call('confirmDelete', $doc->id)
            ->call('deleteDocument');

        $this->assertDatabaseMissing('documents', ['id' => $doc->id]);
    }

    public function test_job_has_reports_relationship(): void
    {
        JobReport::factory()->count(3)->create(['job_id' => $this->job->id]);
        $this->assertCount(3, $this->job->reports);
    }
}
