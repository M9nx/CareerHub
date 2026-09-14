<?php

namespace App\Http\Requests\Employee;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Employee;
    }

    public function rules(): array
    {
        return [];
    }
}
