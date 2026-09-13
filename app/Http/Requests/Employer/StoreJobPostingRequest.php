<?php

namespace App\Http\Requests\Employer;

use App\Models\JobPosting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobPostingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', JobPosting::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'status' => [
                'required',
                Rule::in([
                    'draft',
                    'published',
                    'closed',
                    'archived',
                ]),
            ],
        ];
    }
}
