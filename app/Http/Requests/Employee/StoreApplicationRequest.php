<?php

namespace App\Http\Requests\Employee;

use App\Models\Application;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'job_posting_id' => [
                'required',
                'exists:job_postings,id',

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