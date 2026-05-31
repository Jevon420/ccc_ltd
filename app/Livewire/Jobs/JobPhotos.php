<?php

namespace App\Livewire\Jobs;

use App\Models\Job;
use App\Traits\Livewire\HasToast;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class JobPhotos extends Component
{
    use HasToast, WithFileUploads;

    public Job $job;

    /** @var TemporaryUploadedFile[] */
    public array $photos = [];

    public ?int $confirmingDeleteId = null;

    protected function rules(): array
    {
        return [
            'photos.*' => ['image', 'max:10240'], // 10 MB per photo
        ];
    }

    public function upload(): void
    {
        abort_unless(auth()->user()->can('job_photos.upload'), 403);

        $this->validate();

        foreach ($this->photos as $photo) {
            $this->job
                ->addMedia($photo->getRealPath())
                ->usingFileName($photo->getClientOriginalName())
                ->toMediaCollection('photos');
        }

        $count = count($this->photos);
        $this->photos = [];
        $this->toastSuccess("{$count} photo(s) uploaded successfully.");
    }

    public function confirmDelete(int $mediaId): void
    {
        abort_unless(auth()->user()->can('job_photos.delete'), 403);
        $this->confirmingDeleteId = $mediaId;
    }

    public function deletePhoto(): void
    {
        abort_unless(auth()->user()->can('job_photos.delete'), 403);

        $media = $this->job->getMedia('photos')->firstWhere('id', $this->confirmingDeleteId);

        if ($media) {
            $media->delete();
            $this->toastSuccess('Photo deleted.');
        }

        $this->confirmingDeleteId = null;
    }

    public function render(): View
    {
        abort_unless(auth()->user()->can('job_photos.view'), 403);

        return view('livewire.jobs.job-photos', [
            'media' => $this->job->getMedia('photos'),
        ]);
    }
}
