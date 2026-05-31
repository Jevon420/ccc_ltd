<?php

namespace App\Livewire\Public;

use App\Mail\ContactRequestReceived;
use App\Models\ContactRequest;
use App\Models\Setting;
use App\Traits\Livewire\HasToast;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class ContactForm extends Component
{
    use HasToast;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $service = '';

    public string $message = '';

    public bool $submitted = false;

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'service' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:20', 'max:2000'],
        ];
    }

    public function submit(): void
    {
        $this->validate();

        $contactRequest = ContactRequest::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'service' => $this->service,
            'message' => $this->message,
        ]);

        $notifyEmail = Setting::get('company_email', config('mail.from.address'));

        Mail::to($notifyEmail)->send(new ContactRequestReceived($contactRequest));

        $this->submitted = true;
        $this->reset(['name', 'email', 'phone', 'service', 'message']);
        $this->toastSuccess('Your request has been submitted. We\'ll be in touch within 1 business day.');
    }

    public function render()
    {
        return view('livewire.public.contact-form');
    }
}
