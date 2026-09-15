<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UpdateProfessionalProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
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
        ];
    }
}
