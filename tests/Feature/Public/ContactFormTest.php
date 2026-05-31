<?php

namespace Tests\Feature\Public;

use App\Livewire\Public\ContactForm;
use App\Mail\ContactRequestReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_page_loads_with_livewire_form(): void
    {
        $this->get(route('contact'))
            ->assertStatus(200)
            ->assertSeeLivewire(ContactForm::class);
    }

    public function test_form_validates_required_fields(): void
    {
        Mail::fake();

        Livewire::test(ContactForm::class)
            ->call('submit')
            ->assertHasErrors(['name', 'email', 'service', 'message']);

        Mail::assertNothingSent();
    }

    public function test_form_validates_message_minimum_length(): void
    {
        Mail::fake();

        Livewire::test(ContactForm::class)
            ->set('name', 'John Smith')
            ->set('email', 'john@example.com')
            ->set('service', 'Land Maintenance')
            ->set('message', 'Too short.')
            ->call('submit')
            ->assertHasErrors(['message']);
    }

    public function test_successful_submission_saves_to_database(): void
    {
        Mail::fake();

        Livewire::test(ContactForm::class)
            ->set('name', 'Jane Doe')
            ->set('email', 'jane@example.com')
            ->set('phone', '+1 868 555-1234')
            ->set('service', 'Debris Cleaning/Removal')
            ->set('message', 'We need full debris removal from a residential site in San Fernando after recent renovations.')
            ->call('submit')
            ->assertSet('submitted', true)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('contact_requests', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'service' => 'Debris Cleaning/Removal',
            'status' => 'new',
        ]);
    }

    public function test_successful_submission_sends_notification_email(): void
    {
        Mail::fake();

        Livewire::test(ContactForm::class)
            ->set('name', 'Carlos Rivera')
            ->set('email', 'carlos@example.com')
            ->set('service', 'Rural Development')
            ->set('message', 'Looking for rural road development support in the Sangre Grande area for our agricultural cooperative.')
            ->call('submit');

        Mail::assertQueued(ContactRequestReceived::class, function (ContactRequestReceived $mail) {
            return $mail->contactRequest->email === 'carlos@example.com';
        });
    }

    public function test_form_resets_after_successful_submission(): void
    {
        Mail::fake();

        Livewire::test(ContactForm::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('service', 'Land Maintenance')
            ->set('message', 'Ongoing maintenance contract needed for a 5-acre commercial property in Chaguanas.')
            ->call('submit')
            ->assertSet('name', '')
            ->assertSet('email', '')
            ->assertSet('service', '');
    }
}
