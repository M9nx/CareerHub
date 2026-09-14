<?php

namespace App\Http\Requests\Employee;

use App\Enums\JobPostingStatus;
use App\Models\Application;
use App\Models\JobPosting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\Unique;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Application::class) ?? false;
    }

    /**
     * @return array<string, list<string|Exists|Unique>>
     */
    public function rules(): array
    {
        return [
            'job_posting_id' => [
                'required',
                Rule::exists(JobPosting::class, 'id')->where(
                    fn ($query) => $query
                        ->where('status', JobPostingStatus::Published)
                        ->where('is_active', true)
                ),
                Rule::unique(Application::class, 'job_posting_id')
                    ->where(fn ($query) => $query->where(
                        'employee_id',
                        $this->user()->id,
                    )),
            ],
            'cover_letter' => [
                'nullable',
                'string',
            ],
        ];
    }
}
