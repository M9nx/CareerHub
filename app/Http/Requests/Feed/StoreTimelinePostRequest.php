<?php

namespace App\Http\Requests\Feed;

use App\Http\Requests\Concerns\RemovesEmptyFileUploads;
use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;

class StoreTimelinePostRequest extends FormRequest
{
    use RemovesEmptyFileUploads;

    public function authorize(): bool
    {
        return $this->user()?->can('create', Post::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->removeEmptyFileUploadArrays(['attachments']);
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => [
                'file',
                'max:5120',
                'mimes:jpg,jpeg,png,pdf',
            ],
        ];
    }
}
