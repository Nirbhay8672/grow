<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PriorityTypeUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('role.update') ?? false;
    }

    public function rules(): array
    {
        $priorityTypeId = $this->route('priorityType')->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('priority_types', 'name')->ignore($priorityTypeId)
            ],
            'is_active' => ['boolean'],
        ];
    }
}

