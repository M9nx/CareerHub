<?php

namespace App\Http\Requests\Employee;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UpdateEmployeeProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Employee;
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
