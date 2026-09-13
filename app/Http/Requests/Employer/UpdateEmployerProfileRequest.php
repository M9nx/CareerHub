<?php

namespace App\Http\Requests\Employer;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployerProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Employer;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
        ];
    }
}
