<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StateUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('role.update') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:3',
                Rule::unique('states', 'code')->ignore($this->route('state')->id),
            ],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}