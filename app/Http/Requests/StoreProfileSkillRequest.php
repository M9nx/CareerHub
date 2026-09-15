<?php

namespace App\Http\Requests;

use App\Models\ProfileSkill;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreProfileSkillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', ProfileSkill::class);
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
        ];
    }
}
