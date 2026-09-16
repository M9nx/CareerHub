<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\RemovesEmptyFileUploads;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UpdateProfessionalProfileRequest extends FormRequest
{
    use RemovesEmptyFileUploads;

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->removeEmptyFileUploads(['avatar', 'cover']);
    }

    /**
     * @return array<string, list<string|File>>
     */
    public function rules(): array
    {
        return [
            'headline' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'about' => ['nullable', 'string', 'max:5000'],
            'avatar' => [
                'nullable',
                File::types(['jpg', 'jpeg', 'png', 'webp'])->max(2 * 1024),
            ],
            'cover' => [
                'nullable',
                File::types(['jpg', 'jpeg', 'png', 'webp'])->max(4 * 1024),
            ],
        ];
    }
}
