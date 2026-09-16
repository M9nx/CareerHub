<?php

namespace App\Http\Requests\Employer;

use App\Enums\UserRole;
use App\Http\Requests\Concerns\RemovesEmptyFileUploads;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UpdateEmployerProfileRequest extends FormRequest
{
    use RemovesEmptyFileUploads;

    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Employer;
    }

    protected function prepareForValidation(): void
    {
        $this->removeEmptyFileUploads(['logo']);
    }

    /**
     * @return array<string, list<string|File>>
     */
    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:255'],
            'company_size' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255', 'url'],
            'about' => ['nullable', 'string', 'max:5000'],
            'logo' => [
                'nullable',
                File::types(['jpg', 'jpeg', 'png', 'webp'])->max(2 * 1024),
            ],
        ];
    }
}
