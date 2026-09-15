<?php

namespace App\Http\Requests\Feed;

use Illuminate\Foundation\Http\FormRequest;

class ShareTimelinePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('share', $this->route('post')) ?? false;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
