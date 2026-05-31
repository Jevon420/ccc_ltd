<?php

namespace App\Livewire\Documents;

use App\Models\Document;
use App\Traits\Livewire\HasToast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class DocumentManager extends Component
{
    use HasToast, WithFileUploads;

    public Model $documentable;  // the parent: Job, Client, Quote, etc.

    public bool $isOpen = false;

    public string $title = '';

    public string $category = '';

    public string $notes = '';

    public mixed $file = null;

    public ?int $confirmingDeleteId = null;

    public static array $categories = [
        'Contract', 'Permit', 'Quote', 'Invoice', 'Report',
        'Certificate', 'Insurance', 'Photo', 'Other',
    ];

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'file' => ['required', 'file', 'max:51200',
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,webp,zip'],
        ];
    }

    public function openUpload(): void
    {
        abort_unless(auth()->user()->can('documents.upload'), 403);
        $this->reset(['title', 'category', 'notes', 'file']);
        $this->isOpen = true;
    }

    public function upload(): void
    {
        abort_unless(auth()->user()->can('documents.upload'), 403);

        $this->validate();

        $document = Document::create([
            'documentable_type' => get_class($this->documentable),
            'documentable_id' => $this->documentable->id,
            'title' => $this->title,
            'category' => $this->category ?: null,
            'notes' => $this->notes ?: null,
        ]);

        $document->addMedia($this->file->getRealPath())
            ->usingFileName($this->file->getClientOriginalName())
            ->toMediaCollection('file');

        $this->isOpen = false;
        $this->reset(['title', 'category', 'notes', 'file']);
        $this->toastSuccess("Document \"{$document->title}\" uploaded successfully.");
    }

    public function confirmDelete(int $id): void
    {
        abort_unless(auth()->user()->can('documents.delete'), 403);
        $this->confirmingDeleteId = $id;
    }

    public function deleteDocument(): void
    {
        abort_unless(auth()->user()->can('documents.delete'), 403);

        $doc = Document::findOrFail($this->confirmingDeleteId);
        $doc->delete();
        $this->confirmingDeleteId = null;
        $this->toastSuccess('Document deleted.');
    }

    public function render(): View
    {
        abort_unless(auth()->user()->can('documents.view'), 403);

        $documents = Document::with('media')
            ->where('documentable_type', get_class($this->documentable))
            ->where('documentable_id', $this->documentable->id)
            ->latest()
            ->get();

        return view('livewire.documents.document-manager', compact('documents'));
    }
}
