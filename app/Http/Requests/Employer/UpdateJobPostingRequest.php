<?php

namespace App\Http\Requests\Employer;

use App\Models\JobPosting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJobPostingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $jobPosting = $this->route('job');

        return $jobPosting instanceof JobPosting
            && ($this->user()?->can('update', $jobPosting) ?? false);
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
