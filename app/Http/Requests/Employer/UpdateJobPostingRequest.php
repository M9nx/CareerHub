<?php

namespace App\Http\Requests\Employer;

use App\Enums\EmploymentType;
use App\Enums\JobPostingStatus;
use App\Models\JobPosting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateJobPostingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $jobPosting = $this->route('job');

        return $jobPosting instanceof JobPosting
            && ($this->user()?->can('update', $jobPosting) ?? false);
    }

    /**
     * @return array<string, list<string|Enum>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'employment_type' => ['nullable', Rule::enum(EmploymentType::class)],
            'status' => ['required', Rule::enum(JobPostingStatus::class)],
            'employer_id' => ['prohibited'],
        ];
    }
}
