<?php

namespace App\Http\Requests\Employee;

use App\Enums\UserRole;
use App\Http\Requests\Concerns\RemovesEmptyFileUploads;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UpdateEmployeeProfileRequest extends FormRequest
{
    use RemovesEmptyFileUploads;

    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Employee;
    }

    protected function prepareForValidation(): void
    {
        $this->removeEmptyFileUploads(['cv', 'application_image']);
    }

    /**
     * @return array<string, list<string|File>>
     */
    public function rules(): array
    {
        return [
            'cv' => [
                'nullable',
                File::types(['pdf'])->max(5 * 1024),
            ],
            'application_image' => [
                'nullable',
                File::types(['jpg', 'jpeg', 'png'])->max(2 * 1024),
            ],
        ];
    }
}
